<?php
/**
 * (c) Toyosi Oyelayo <toyosioyelayo@gmail.com>
 */

 return [
     /**
      * Integration method. Could be webpay, webdirect or collegepay. Default is webpay
      */
     'gateway_type' => env('INTERSWITCH_GATEWAY_TYPE', 'WEBPAY'),

     /**
      * Product ID provided by Interswitch
      */
     'product_id' => env('INTERSWITCH_PRODUCT_ID'),

     /**
      * Pay Item ID provided by Interswitch
      */
     'pay_item_id' => env('INTERSWITCH_PAY_ITEM_ID'),

     /**
      * Currency, Naira is default
      */
     'currency' => env('INTERSWITCH_CURRENCY', 566),

     /**
      * Site redirection url
      */
     'site_redirect_url' => env('INTERSWITCH_SITE_REDIRECT_URL'),

     /**
      * current environment (test or live)
      */
      'env' => env('INTERSWITCH_ENV', 'TEST'),

     /**
      *  Test environment parameters
      */
      'test' => [
          'webpay' => [
              'macKey' => 'D3D1D05AFE42AD50818167EAC73C109168A0F108F32645C8B59E897FA930DA44F9230910DAC9E20641823799A107A02068F7BC0F4CC41D2952E249552255710F',
              'initializationURL' => 'https://sandbox.interswitchng.com/webpay/pay',
              'transactionStatusURL' => 'https://sandbox.interswitchng.com/webpay/api/v1/gettransaction.json',
              'productID' => 6205,
              'payItemID' => 101
          ]
      ]
 ];