<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interswitch Transaction Logs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" />
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12 mb-3">
                <h3>Transaction Logs</h3>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body table-responsive">
                        <table id="logs" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Customer ID</th>
                                    <th>Customer Name</th>
                                    <th>Customer Email</th>
                                    <th>Transaction Reference</th>
                                    <th>Amount</th>
                                    <th>Response Code</th>
                                    <th>Response Text</th>
                                    <th>Environment</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td>{{ $log['customer_id'] }}</td>
                                        <td>{{ $log['customer_name'] }}</td>
                                        <td>{{ $log['customer_email'] }}</td>
                                        <td>{{ $log['transaction_reference'] }}</td>
                                        <td>&#8358 {{ number_format(($log['amount_in_kobo'] / 100), 2) }}</td>
                                        <td>{{ $log['response_code'] }}</td>
                                        <td>{{ $log['response_text'] }}</td>
                                        <td>{{ $log['environment'] }}</td>
                                        <td>{{ $log['created_at'] }}</td>
                                        <td></td>
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
        } );
    </script>
</body>
</html>