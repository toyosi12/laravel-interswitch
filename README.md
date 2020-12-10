# laravel-interswitch

[![Issues](	https://img.shields.io/github/issues/toyosi12/laravel-interswitch)](https://github.com/toyosi12/laravel-interswitch/issues)
[![Forks](	https://img.shields.io/github/forks/toyosi12/laravel-interswitch)](https://github.com/toyosi12/laravel-interswitch/forks)
[![Stars](	https://img.shields.io/github/stars/toyosi12/laravel-interswitch)](https://github.com/toyosi12/laravel-interswitch/stars)

> A laravel package to easily integrate interswitch

## Installation

[PHP](https://php.net) 7.2+ and [Composer](https://getcomposer.org) are required.

To get the latest version of Laravel Interswitch, simply require it

```bash
composer require toyosi/laravel-interswitch
```
Once installed, the package automatically registers its service provider and facade.

Next, run migration to get the database table that logs all transactions:

```bash
php artisan migrate
```

## Configuration
After the installation, a configuration file 'interswitch.php' with some defaults is placed in your config directory.

With this package, you can easily integrate the three major payment types of interswitch which are webpay, paydirect or collegepay. Webpay is the default type.

## Payment Flow
The payment flow described below applies to interswitch and many other payment gateways

1. User clicks a button to make payment, the user is redirected to the payment provider's site, usually by submitting a form with hidden fields. A hash is generated from these fields.
2. On the payment provider's site, card details are entered.
3. The user is redirected back with details of the transaction indicating a successful or failed transaction.

## Usage

### Test Environment

### 1. Open .env and add:
```php
INTERSWITCH_SITE_REDIRECT_URL="${APP_URL}/response"
```
This is the only variable in the test environment that is required. 'response' as indicated above could be anything. The specified value indicates the url the user is redirected to after every transaction.
Note: please ensure APP_URL is correctly defined.

### 2. Create payment route and view
Create your payment route in web.php. Something like: 
```php
Route::get('pay', function(){
  return view('payment');
})
```
Then create the view. In this case, 'payment.blade.php'. The view can be like so:
```html
<form action="interswitch-pay" method="post">
    <input type="hidden" name="customerName" value="Toyosi Oyelayo" />
    <input type="hidden" name="customerID" value="1" />
    <input type="hidden" name="customerEmail" value="toyosioyelayo@gmail.com" />
    <input type="hidden" name="amount" value="12000" />
    <button type="submit"
    style="padding: 10px 20px; background-color: #ff0000; border: none; color: #fff">Pay Now</button>
</form>
```

Note that the form is submitted to 'interswitch-pay', this is predefined in the package.
All the fields are required. On clicking the 'Pay Now' button, the user is redirected to interswitch's payment page, where card details are entered. The user is then redirected back to your website as indicated by 'INTERSWITCH_SITE_REDIRECT_URL'.
A list of test cards [can be found here](https://sandbox.interswitchng.com/docbase/docs/webpay/test-cards).


### Live Environment
The same processes described in the test environment above also applies to the live environment. Do note that Interswitch does certain checks on your website before it can be approved to recieve live payments:
1. You must have the interswitch logo on your website
2. You must have filled and submitted the User Acceptance Test Form which has to be approved by interswitch. You can [download the form here](https://sandbox.interswitchng.com/docbase/docs/webpay/merchant-user-acceptance-testing)
3. After this, you are given your unique Product ID, MAC ID and Pay Item ID. These have to be included in your .env file like so:

```php
INTERSWITCH_PRODUCT_ID=
INTERSWITCH_MAC_KEY=
INTERSWITCH_PAY_ITEM_ID=
```

You also have to change the environment to live like so:
```php
INTERSWITCH_ENV=LIVE
```

To change the integration type, use:
```php
INTERSWITCH_GATEWAY_TYPE=
```
The values could be 'WEBPAY', 'PAYDIRECT' or 'COLLEGEPAY'. 'WEBPAY' is the default.

## Split Payment
With split payment, you can divide money recieved on your site into multiple accounts. This is only available on COLLEGEPAY. Split implements uses XML which I have handled in the package. You can setup split payments in two easy steps:
### 1. Enable split payments in .env like so:
```php
 INTERSWITCH_SPLIT=true
 ```
 ### 2. Configure accounts and percentage allocation.
 You need to specify the account numbers to be credited and percentage of the total amount to be credited into each account. To do this, open 'config/interswitch.php' and edit the key 'splitDetails'. Do note that this key already exists, you only need to edit it:
 ```php
 'splitDetails' => [
          [
            'itemName' => 'item1',
            'bankID' => 7,
            'accountNumber' => 1234567890,
            'percentageAllocation' => 50

        ],
        [
            'itemName' => 'item2',
            'bankID' => 10,
            'accountNumber' => 4564567890,
            'percentageAllocation' => 50
        ]
      ],
 ```
 In the above example, two bank accounts are indicated and the total amount is split into two equal parts (50% each) as indicated with 'percentageAllocation'. In the test environment, 'accountNumber' can be any 10 digit number. Don't forget to change to valid account numbers in the live environment. The package handles the conversion into XML and other necessary stuffs.
 Note: You can find the [list of bank IDs here](https://sandbox.interswitchng.com/docbase/docs/collegepay-web/xml-split-bank-codes)
 
 ## Transaction Logs
 You can find all transaction logs at the 'interswitch-logs' route. Don't forget to protect this route. You don't want just any user to have access to it.
 
 ### Requerying Transactions
 Sometimes, things might go wrong while a user is making payment. It could be power failure or flaky internet connectivity. To complete an already started payment process, you can click the 'requery' button in 'interswitch logs'. This updates the transaction as necessary.
 
 ## Contributing
 Do feel free to fork this repo and contribute by submitting a pull request. Let's make this better.
 
 ## Star
 I'd love you star this repo. Also [follow me on twitter](https://twitter.com/dev_toyosi)
 
 ## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.






