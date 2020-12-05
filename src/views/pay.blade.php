<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
</head>
<body>
    <h4>Loading...</h4>
    <form action="{{$transactionData['initializationURL']}}" id="initializationForm" method="post">
        <input name="product_id" type="hidden" value="{{$transactionData['productID']}}" />
        <input name="pay_item_id" type="hidden" value="{{$transactionData['payItemID']}}" />
        <input name="amount" type="hidden" value="530000" />
        <input name="currency" type="hidden" value="{{$transactionData['currency']}}" />
        <input name="site_redirect_url" type="hidden" value="{{$transactionData['siteRedirectURL']}}"/>
        <input name="txn_ref" type="hidden" value="{{$transactionData['transactionReference']}}" />
        <input name="cust_id" type="hidden" value="{{$transactionData['customerID']}}" >
        <input name="cust_name" type="hidden" value="{{$transactionData['customerName']}}" />
        <input name="hash" type="hidden" value="{{$transactionData['hash']}}" />

        @if(config('interswitch.split'))
            <input name="payment_params" type="hidden" value="payment_split" />
            <input name="xml_data" type="hidden" value="{!! $transactionData['splitData'] !!}" />
        @endif
        
    </form>
</body>
<script>
//submit form onload
window.onload = function(){
    document.getElementById('initializationForm').submit();
}
</script>
</html>