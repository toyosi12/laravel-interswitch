<?php
namespace Toyosi\Interswitch\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Toyosi\Interswitch\Interswitch;

class InterswitchController extends Controller{
    public function pay(Request $request){
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
        $interswitch = new Interswitch;
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
        print_r($response);
    }


}