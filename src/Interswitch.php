<?php
namespace Toyosi\Interswitch;

class Interswitch{
    /**
     * The current envirionment(test or live).
     */
    private $env;

    /**
     * The url for redirection after payment.
     */
    private $siteRedirectURL;

    /**
     * The gateway type - webpay, paydirect or collegepay.
     */
    private $gatewayType;

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


    public function __construct(){
       $this->env = config('interswitch.env');
       $this->siteRedirectURL = config('interswitch.site_redirect_url');
       $this->gatewayType = config('interswitch.gateway_type');
       $this->currency = config('interswitch.currency');
       $this->environmentSelector();
    }


    /**
     * This method gets all the required data to be supplied to 
     * the interswitch payment page
     */
    public function initializeTransaction($request){
        $transactionReference = $this->generateTransactionReference();
        $amountInKobo = $request['amount'] * 100;
        $hash = $this->generateTransactionHash($amountInKobo, $transactionReference);

        /**
         * Data supplied to the interswitch interface
         */
        $computedData = [
            'transactionReference' => $transactionReference,
            'productID' => $this->productID,
            'payItemID' => $this->payItemID,
            'amount' => $amountInKobo,
            'siteRedirectURL' => $this->siteRedirectURL,
            'macKey' => $this->macKey,
            'currency' => $this->currency,
            'customerID' => $request['customerID'],
            'customerName' => $request['customerName'],
            'hash' => $hash,
            'initializationURL' => $this->initializationURL
        ];

        return $computedData;
    }

    /**
     * This method generates a unique reference per transaction.
     * It concatenates the current timestamp, to ensure it is unique
     */
    private function generateTransactionReference(){
        $length = 5;
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
        $paramters = $_transactionReference . $this->productID . $this->payItemID . $_amountInKobo . $this->siteRedirectURL . $this->macKey;
        $hash = hash('SHA512', $paramters);
        return $hash;
    }

    /**
     * changes configurations based on the current environment(live or test)
     */
    private function environmentSelector(){
        if($this->env == 'TEST'){
            if($this->gatewayType == 'WEBPAY'){
                $this->productID = config('interswitch.test.webpay.productID');
                $this->payItemID = config('interswitch.test.webpay.payItemID');
                $this->macKey = config('interswitch.test.webpay.macKey');
                $this->transactionStatusURL = config('interswitch.test.webpay.transactionStatusURL');
                $this->initializationURL = config('interswitch.test.webpay.initializationURL');
            }
        }else if($this->env == 'LIVE'){
            $this->productID = config('interswitch.live.productID');
            $this->payItemID = config('interswitch.live.payItemID');
            $this->macKey = config('interswitch.live.macKey');
            $this->transactionStatusURL = config('interswitch.live.transactionStatusURL');
            $this->initializationURL = config('interswitch.live.initializationURL');
        }
    }
}