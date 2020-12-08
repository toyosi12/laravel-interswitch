@component('mail::message')
# {{$message['responseDescription']}}
<div>
    <p>Dear {{$message['customerName']}}, <br />
    Your payment of &#8358 {{number_format(($message['amount']) / 100, 2)}} was successful. <br />
    Payment Reference - {{$message['paymentReference']}}
    </p>
</div>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
