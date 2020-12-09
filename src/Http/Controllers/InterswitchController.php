<?php
namespace Toyosi\Interswitch\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Toyosi\Interswitch\Interswitch;
use Toyosi\Interswitch\Mail\InterswitchMailable;
use Toyosi\Interswitch\Models\InterswitchPayment;
use Toyosi\Interswitch\Exceptions\IntegrationTypeException;

class InterswitchController extends Controller{
    
    public function pay(Request $request){
        $interswitch = new Interswitch;
        /**
         * Verify that a valid integration is used
         */
        $interswitch->verifyGateway();

        /**
         * customerId, customerName, customerEmail and amount are 
         * all expected to be passed throught the form.
         * They are all required.
         * 
         * Amount must be greater than 0 and must be a number
         */
        $validator = Validator::make($request->all(), [
            'customerID' => 'required|numeric',
            'customerName' => 'required|string',
            'customerEmail' => 'required|email',
            'amount' => 'required|gt:0|numeric'
        ]);

        /**
         * If validation fails, return error
         */
        
        if($validator->fails()){
            return $validator->errors();
        }

            
        /**
         * if all validations are passed,
         * send request data to Interswitch class to initialize transaction.
         * This is the beginning of the phase where the user is redirected to 
         * the payment page provided by interswitch
         */
        $transactionData = $interswitch->initializeTransaction($request->all());

        /**
         * Return to hidden forms (with the required data)
         * This sends a post request to interswitch servers
         * which causes the user to be redirected to the payment page(where they'd enter their card details)
         */
         return view('interswitch::pay', compact('transactionData'));
    }

    /**
     * Redirect the user after payment attempt
     */
    public function redirect(Request $request){
        $interswitch = new Interswitch;
        $response = $interswitch->getTransactionStatus($request->all());


        $rebuiltResponse = [
            'paymentReference' => $response['PaymentReference'],
            'responseCode' => $response['ResponseCode'],
            'responseDescription' => $response['ResponseDescription'],
            'amount' => $response['Amount'] / 100,
            'transactionDate' => $response['TransactionDate'],
            'customerEmail' => $response['customerEmail'],
            'customerName' => $response['customerName']
        ];


        /**
         * Send email to user on successful transaction if email notification is enabled
         */
        if(in_array($rebuiltResponse['responseCode'], ['00', '10', '11'])){
            Mail::to($rebuiltResponse['customerEmail'])->send(new InterswitchMailable($rebuiltResponse));
        }

        $redirectURL = $interswitch->attachQueryString($rebuiltResponse);

        return redirect()->to($redirectURL);
    }

    public function requeryTransaction(Request $request){
        $interswitch = new Interswitch;

        $validator = Validator::make($request->all(), [
            'txnref' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => "Requery Failed",
                'data' => $validator->errors()
            ]);
        }

        $response = $interswitch->getTransactionStatus($request->all());

        return response()->json([
            'success' => true,
            'message' => "Requery Successful",
            'data' => $response
        ]);
        
    }

    public function logs(){
        $logs = InterswitchPayment::all()->sortByDesc("created_at");;
        return view('interswitch::logs', compact('logs'));
    }


}