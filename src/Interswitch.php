<?php
namespace Toyosi\Interswitch;
use Toyosi\Interswitch\Models\InterswitchPayment;
use Toyosi\Interswitch\Exceptions\SplitPaymentException;

class Interswitch{
    /**
     * The current envirionment(test or live).
     */
    private $env;

    /**
     * The url for redirection after payment(used internally by the system).
     */
    private $fixedRedirectURL;

    /**
     * User defined redirect url
     */
    private $siteRedirectURL;

    /**
     * The gateway type - webpay, paydirect or collegepay.
     */
    private $gatewayType;

    /**
     * Unique reference for transaction
     */
    private $transactionReference;

    /**
     * The currency being used, Naira is the default.
     */
    private $currency;

     /**
      * mac key as provided by interswitch
      */
    private $macKey;

    /**
     * product ID as provided by interswitch
     */
    private $productID;

    /**
     * pay item ID as provided by interswitch
     */
    private $payItemID;

    /**
     * URL where user is directed to to make payment
     */
    private $initializationURL;

    /**
     * URL to check the status of transaction
     */
    private $transactionStatusURL;

    /**
     * split or not
     */
    private $split;

    /**
     * Configurations for split payment
     */
    private array $splitDetails;

    /**
     * Name of institution (College pay split payment)
     */
    private $college;

    private $amountInKobo;

    /**
     * Send mail after successful transaction or not
     */
    private $sendMail;

    const GATEWAY_TYPES = ['WEBPAY', 'COLLEGEPAY', 'PAYDIRECT'];

    public function __construct(){
        $this->env = config('interswitch.env');
        $this->fixedRedirectURL = env('APP_URL') . '/' .config('interswitch.fixedRedirectURL');
        $this->siteRedirectURL = config('interswitch.siteRedirectURL');
        $this->gatewayType = config('interswitch.gatewayType');
        $this->currency = config('interswitch.currency');
        $this->split = config('interswitch.split');
        $this->college = config('interswitch.college');
        $this->transactionReference = $this->generateTransactionReference();
        $this->sendMail = config('interswitch.send_mail');
        $this->environmentSelector();
        $this->splitDetails = config('interswitch.splitDetails');
    }

    /**
     * Verify that a valid integration is used
     */
    public function verifyGateway(){
        if(!in_array($this->gatewayType, self::GATEWAY_TYPES)){
            throw new IntegrationTypeException("Unrecongnized Integration Type");
        }
    }


    /**
     * This method gets all the required data to be supplied to 
     * the interswitch payment page
     */
    public function initializeTransaction($request){
        $this->amountInKobo = $request['amount'] * 100;
        $hash = $this->generateTransactionHash($this->amountInKobo, $this->transactionReference);

        /**
         * save payment data into the database
         */
        InterswitchPayment::create([
            'customer_id' => $request['customerID'],
            'customer_name' => $request['customerName'],
            'customer_email' => $request['customerEmail'],
            'transaction_reference' => $this->transactionReference,
            'environment' => $this->env,
            'response_code' => -1,
            'response_text' => 'Pending',
            'amount_in_kobo' => $this->amountInKobo,
        ]);
         

        /**
         * Data supplied to the interswitch interface
         */
        $computedData = [
            'transactionReference' => $this->transactionReference,
            'productID' => $this->productID,
            'payItemID' => $this->payItemID,
            'amount' => $this->amountInKobo,
            'siteRedirectURL' => $this->fixedRedirectURL,
            'macKey' => $this->macKey,
            'currency' => $this->currency,
            'customerID' => $request['customerID'],
            'customerName' => $request['customerName'],
            'hash' => $hash,
            'initializationURL' => $this->initializationURL,
            'splitData' => $this->generateXMLForSplitPayments()
        ];
        

        return $computedData;
    }


    /**
     * Get the status of a transaction
     */
    public function getTransactionStatus($request){
        /**
         * Generate required hash using SHA512 algorithm
         */
        $hash = hash('SHA512', $this->productID . $request['txnref'] . $this->macKey);
        $transactionDetails = InterswitchPayment::where('transaction_reference', $request['txnref'])->first();
        $this->amountInKobo = $transactionDetails['amount_in_kobo'];
        $queryString = '?productId=' . $this->productID . "&transactionreference=" . $request['txnref'] . "&amount=". $this->amountInKobo;
        $verificationURL = $this->transactionStatusURL . $queryString;

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $verificationURL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_POST => false, 
        CURLOPT_HTTPHEADER => [
            "content-type: application/json",
            "cache-control: no-cache",
            "Connection: keep-alive",
            "hash: " . $hash
            ],
        ));
      
        $response = json_decode(curl_exec($curl), true);
        $response['customerEmail'] = $transactionDetails['customer_email'];
        $response['customerName'] = $transactionDetails['customer_name'];
        
        /**
         * Update database with transaction status
         */
        InterswitchPayment::where('transaction_reference', $request['txnref'])
                        ->update([
                            'payment_reference' => $response['PaymentReference'],
                            'retrieval_reference_number' => $response['RetrievalReferenceNumber'],
                            'response_code' => $response['ResponseCode'],
                            'response_text' => $response['ResponseDescription']
                        ]);
        return $response;
        
    }

    /**
     * This method generates a unique reference per transaction.
     * It concatenates the current timestamp, to ensure it is unique
     */
    private function generateTransactionReference(){
        $length = 6;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString . time();
    }

    /**
     * Interswitch requires a hash to be generated which using the SHA512 algorithm.
     * The parameters to be hashed are transaction reference, product ID, pay item ID,
     * amount, site redirect url and mac key in that order
     * 
     */
    private function generateTransactionHash($_amountInKobo, $_transactionReference){
        $paramters = $_transactionReference . $this->productID . $this->payItemID . $_amountInKobo . $this->fixedRedirectURL . $this->macKey;
        $hash = hash('SHA512', $paramters);
        return $hash;
    }

    /**
     * This method is used by environmentSelector() to detect whether split payment
     * is enabled or not.
     */
    private function splitDetector(){
        if($this->split){
            return 'split';
        }else{
            return 'noSplit';
        }
    }

    /**
     * This method converts the array of split payment details
     * defined in config/interswitch.php and converts to XML
     */
    public function generateXMLForSplitPayments(){
        if(!$this->split) return;
        
        /**
         * Prevent split if integration is not collegepay
         */
        if($this->gatewayType != 'COLLEGEPAY'){
            throw new SplitPaymentException("Split payment only works with college pay");
        }
        $XMLString = '';
        $XMLDataItems = '';
        $totalPercentageAllocation = 0;
        /**
         * Verify that the total percentage allocation is exactly 100
         */
        foreach($this->splitDetails as $splitDetail){
            $totalPercentageAllocation += $splitDetail['percentageAllocation'];
        }
        if($totalPercentageAllocation !== 100){
            throw new SplitPaymentException("Total percentage allocation is expected to be 100");
        }

        /**
         * convert to XML
         */
        foreach($this->splitDetails as $index => $splitDetail){
            $itemID = $index + 1;

            $splitDetail = (object) $splitDetail;

            /**
             * In split payment, The total amount to be disbursed to banks must be total amount lef
             * after deducting the N300 charge
             */
            $itemAmount = ($this->amountInKobo - 30000) * ($splitDetail->percentageAllocation / $totalPercentageAllocation);


            $XMLDataItems .= "
                <item_detail   
                    item_id='$itemID'
                    item_name='$splitDetail->itemName'
                    item_amt='$itemAmount'
                    bank_id='$splitDetail->bankID'
                    acct_num='$splitDetail->accountNumber'
                    />
            ";
            $XMLString = "
                <payment_item_detail>
                    <item_details detail_ref='$this->transactionReference' college='xyzdfd'>
                        {$XMLDataItems}
                    </item_details>
                </payment_item_detail>
            ";
        }
        return $XMLString;
    }

    /**
     * changes configurations based on the current environment(live or test)
     */
    private function environmentSelector(){
        if($this->env === 'TEST'){
            if($this->gatewayType === 'WEBPAY'){
                $this->productID = config("interswitch.test.{$this->splitDetector()}.webPay.productID");
                $this->payItemID = config("interswitch.test.{$this->splitDetector()}.webPay.payItemID");
                $this->macKey = config("interswitch.test.{$this->splitDetector()}.webPay.macKey");
                $this->transactionStatusURL = config("interswitch.test.{$this->splitDetector()}.webPay.transactionStatusURL");
                $this->initializationURL = config("interswitch.test.{$this->splitDetector()}.webPay.initializationURL");
            }else if($this->gatewayType === 'PAYDIRECT'){
                $this->productID = config("interswitch.test.{$this->splitDetector()}.payDirect.productID");
                $this->payItemID = config("interswitch.test.{$this->splitDetector()}.payDirect.payItemID");
                $this->macKey = config("interswitch.test.{$this->splitDetector()}.payDirect.macKey");
                $this->transactionStatusURL = config("interswitch.test.{$this->splitDetector()}.payDirect.transactionStatusURL");
                $this->initializationURL = config("interswitch.test.{$this->splitDetector()}.payDirect.initializationURL");
            }else if($this->gatewayType === 'COLLEGEPAY'){
                $this->productID = config("interswitch.test.{$this->splitDetector()}.collegePay.productID");
                $this->payItemID = config("interswitch.test.{$this->splitDetector()}.collegePay.payItemID");
                $this->macKey = config("interswitch.test.{$this->splitDetector()}.collegePay.macKey");
                $this->transactionStatusURL = config("interswitch.test.{$this->splitDetector()}.collegePay.transactionStatusURL");
                $this->initializationURL = config("interswitch.test.{$this->splitDetector()}.collegePay.initializationURL");
            }
        }else if($this->env === 'LIVE'){
            $this->productID = config('interswitch.live.productID');
            $this->payItemID = config('interswitch.live.payItemID');
            $this->macKey = config('interswitch.live.macKey');
            $this->transactionStatusURL = config('interswitch.live.transactionStatusURL');
            $this->initializationURL = config('interswitch.live.initializationURL');
        }

    }

    public function attachQueryString($rebuiltResponse){
        $queryString = '/?';
        foreach($rebuiltResponse as $key => $response){
            $queryString .= $key . '=' . $response . '&';
        }

        /**
         * Form the complete url and remove the last character which is '&'
         */
        return substr($this->siteRedirectURL . $queryString, 0, -1);
    }
}