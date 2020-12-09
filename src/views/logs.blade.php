<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interswitch Transaction Logs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
</head>
<body>
    <div class="toast mr-auto bg-dark text-white" style="position: fixed; z-index: 999">
        <div class="toast-body" id="toast_body">
            
        </div>
    </div>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12 mb-3">
                <h3>Transaction Logs</h3>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <table id="logs" class="table table-bordered table-striped table-responsive">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Customer ID</th>
                                    <th>Customer Name</th>
                                    <th>Customer Email</th>
                                    <th>Transaction Reference</th>
                                    <th>Payment Reference</th>
                                    <th>Amount</th>
                                    <th>Response Code</th>
                                    <th>Response Text</th>
                                    <th>Environment</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $key => $log)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $log['customer_id'] }}</td>
                                        <td>{{ $log['customer_name'] }}</td>
                                        <td>{{ $log['customer_email'] }}</td>
                                        <td>{{ $log['transaction_reference'] }}</td>
                                        <td>{{ $log['payment_reference'] }} </td>
                                        <td>&#8358 {{ number_format(($log['amount_in_kobo'] / 100), 2) }}</td>
                                        <td class="response_code">{{ $log['response_code'] }}</td>
                                        <td class="response_text">{{ $log['response_text'] }}</td>
                                        <td>{{ $log['environment'] }}</td>
                                        <td>{{ $log['created_at'] }}</td>
                                        <td><button type="button" class="requery_btn btn btn-secondary" value="{{ $log['transaction_reference'] }}">Requery</button></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#logs').DataTable();

            $('body .requery_btn').on('click', function(){
                $(this).html('Loading').attr('disabled', true);
                let transactionReference = $(this).val();
                fetch('/interswitch-requery', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ txnref: transactionReference })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('data: ', data)
                    $('#toast_body').html(data.message);
                    $('.toast').toast('show');
                    $(this).html('Requery').attr('disabled', false);

                    $(this).parent().parent().find('td.response_code').html(data.data.ResponseCode);
                    $(this).parent().parent().find('td.response_text').html(data.data.ResponseDescription);

                })
                .catch(error => {
                    console.error(error);
                })
            })
        } );
        
    </script>
</body>
</html>