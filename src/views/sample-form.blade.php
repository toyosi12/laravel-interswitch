<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>Interswitch Sample Form</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-6 mx-auto mt-5">
            <div class="card">
                <div class="card-body">
                    <form action="interswitch-pay" method="post">
                        <div class="form-group">
                            <label>Customer Name:</label>
                            <input type="text" class="form-control" name="customerName" value="Toyosi Oyelayo" />
                        </div>
                        <div class="form-group">
                            <label>Customer ID:</label>
                            <input type="text" class="form-control" name="customerID" value="1" />
                        </div>
                        <div class="form-group">
                            <label>Customer Email:</label>
                            <input type="email" class="form-control" name="customerEmail" value="johndoe@gmail.com" />
                        </div>
                        <div class="form-group">
                            <label>Amount:</label>
                            <input type="text" class="form-control" name="amount" value="5000" />
                        </div>
                        <div class="form-group">
                            <button class="btn btn-danger btn-block" type="submit">Pay</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>