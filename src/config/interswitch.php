<?php
/**
 * (c) Toyosi Oyelayo <toyosioyelayo@gmail.com>
 */

 return [
     /**
      * Integration method. Could be WEBPAY, PAYDIRECT or COLLEGEPAY. Default is WEBPAY
      */
     'gateway_type' => env('INTERSWITCH_GATEWAY_TYPE', 'COLLEGEPAY'),

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
      * Site redirection url as defined by user
      */
     'site_redirect_url' => env('INTERSWITCH_SITE_REDIRECT_URL'),

     /**
      * Site redirection path that works internally. Do not change
      */
      'fixed_redirect_url' => 'interswitch-redirect',

     /**
      * current environment (TEST or LIVE)
      */
      'env' => env('INTERSWITCH_ENV', 'TEST'),

      /**
       * Split payment or not
       */
      'split' => env('INTERSWITCH_SPLIT', true),

     /**
      *  Test environment parameters
      */
      'test' => [
          /**
           * Parameters for non-split payments
           */
          'noSplit' => [
                'webPay' => [
                    'macKey' => 'D3D1D05AFE42AD50818167EAC73C109168A0F108F32645C8B59E897FA930DA44F9230910DAC9E20641823799A107A02068F7BC0F4CC41D2952E249552255710F',
                    'initializationURL' => 'https://sandbox.interswitchng.com/webpay/pay',
                    'transactionStatusURL' => 'https://sandbox.interswitchng.com/webpay/api/v1/gettransaction.json',
                    'productID' => 6205,
                    'payItemID' => 101
                ],
                'payDirect' => [
                    'macKey' => 'E187B1191265B18338B5DEBAF9F38FEC37B170FF582D4666DAB1F098304D5EE7F3BE15540461FE92F1D40332FDBBA34579034EE2AC78B1A1B8D9A321974025C4',
                    'initializationURL' => 'https://sandbox.interswitchng.com/webpay/pay',
                    'transactionStatusURL' => 'https://sandbox.interswitchng.com/webpay/api/v1/gettransaction.json',
                    'productID' => 6204,
                    'payItemID' => 103
                ],
                'collegePay' => [
                    'macKey' => 'CEF793CBBE838AA0CBB29B74D571113B4EA6586D3BA77E7CFA0B95E278364EFC4526ED7BD255A366CDDE11F1F607F0F844B09D93B16F7CFE87563B2272007AB3',
                    'initializationURL' => 'https://sandbox.interswitchng.com/webpay/pay',
                    'transactionStatusURL' => 'https://sandbox.interswitchng.com/webpay/api/v1/gettransaction.json',
                    'productID' => 6207,
                    'payItemID' => 103
                ]
          ],
          
          /**
           * Paramters for split payments
           */
          'split' => [
                'webPay' => [
                    'macKey' => 'D3D1D05AFE42AD50818167EAC73C109168A0F108F32645C8B59E897FA930DA44F9230910DAC9E20641823799A107A02068F7BC0F4CC41D2952E249552255710F',
                    'initializationURL' => 'https://sandbox.interswitchng.com/webpay/pay',
                    'transactionStatusURL' => 'https://sandbox.interswitchng.com/webpay/api/v1/gettransaction.json',
                    'productID' => 6205,
                    'payItemID' => 101
                ],
                'payDirect' => [
                    'macKey' => 'E187B1191265B18338B5DEBAF9F38FEC37B170FF582D4666DAB1F098304D5EE7F3BE15540461FE92F1D40332FDBBA34579034EE2AC78B1A1B8D9A321974025C4',
                    'initializationURL' => 'https://sandbox.interswitchng.com/webpay/pay',
                    'transactionStatusURL' => 'https://sandbox.interswitchng.com/webpay/api/v1/gettransaction.json',
                    'productID' => 6204,
                    'payItemID' => 101
                ],
                'collegePay' => [
                    'macKey' => 'CEF793CBBE838AA0CBB29B74D571113B4EA6586D3BA77E7CFA0B95E278364EFC4526ED7BD255A366CDDE11F1F607F0F844B09D93B16F7CFE87563B2272007AB3',
                    'initializationURL' => 'https://sandbox.interswitchng.com/webpay/pay',
                    'transactionStatusURL' => 'https://sandbox.interswitchng.com/webpay/api/v1/gettransaction.json',
                    'productID' => 6207,
                    'payItemID' => 101
                ]
          ]
      ]
 ];