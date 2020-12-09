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
'response' as indicated above could be anything. This is the only variable in the test environment that is required. The specified value indicates the url the user is redirected to after every transaction.
Note: please ensure APP_URL is correctly defined.

### 2. Create payment route and view
Create your route for payment in web.php. Something like: 
```php
Route::get('pay', function(){
  return view('payment');
})
```
Then create the view. In this case, 'payment.blade.php'. The view can be like so:
```html
<form action="interswitch-pay" method="post">
    <input type="hidden" name="customerName" value="Toyosi Oyelayo" />
    <input type="hidden" name="customerID" "1" />
    <input type="hidden" name="customerEmail" />
    <input type="hidden" name="amount" />
    <button type="submit">Pay</button>
</form>
```

All the fields are required. On clicking the 'Pay' button, the user is redirected to interswitch's payment page, where card details are entered. The user is then redirected back to your website as indicated by 'INTERSWITCH_SITE_REDIRECT_URL'.
A list of test cards can be found [here](https://sandbox.interswitchng.com/docbase/docs/webpay/test-cards).


### Live Environment
The same processes described in Test Environment above also applies to the live environment. Do note that Interswitch does certain checks on your website before, it can be approved to recieve live payments.
1. You must have the interswitch logo on your website
2. You must have filled and submitted the User Acceptance Test Form which has to be approved by interswitch. You can download the form [here](https://sandbox.interswitchng.com/docbase/docs/webpay/merchant-user-acceptance-testing)
3. After this, you are given your unique Product ID, MAC ID and Pay Item ID. These have to included in your .env file like so:

```php
INTERSWITCH_PRODUCT_ID=
INTERSWITCH_MAC_KEY=
INTERSWITCH_PAY_ITEM_ID=
```





