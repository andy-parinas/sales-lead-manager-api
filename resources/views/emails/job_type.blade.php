
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <div id="app">
            <!-- Begin page -->
            <div class="container">
                <div class="row">
                    <div class="col-md text-center">
                        <img src="{{ asset('images/homeaddition.png') }}" style="width:350px">
                    </div>
                </div>
                <div class="row" style="height:50px"></div>
                <div class="row">{{ $dateToday }}</div>
                <div class="row" style="height:30px"></div>
                <div class="row">                
                    <div class="col-md"><h1>A new Sales Lead has been assigned to you</h1></div>
                    <div class="col-md">Lead Number: <strong>{{ $leadNumber }}</strong></div>
                    <div class="col-md">Name: <strong>{{ $fullName }}</strong></div>
                    <div class="col-md">Contact Number: <strong>{{ $contactNumber }}</strong></div>
                    <div class="col-md">Address: <strong>{{ $address }}</strong></div>
                    <div class="col-md">Email: <strong>{{ $email }}</strong></div>
                    <div class="col-md">Lead Date: <strong>{{ $leadDate }}</strong></div>
                    <div class="col-md">Product: <strong>{{ $product }}</strong></div>
                    <div class="col-md">Description: <strong>{{ $description }}</strong></div>
                    <div class="col-md">Lead Received Via: <strong>{{ $leadReceivedVia }}</strong></div>
                    <div class="col-md">Lead Source: <strong>{{ $leadSource }}</strong></div>
                </div>
                <div class="row">
                    <div class="col-md">
                    <img src="{{ asset('images/spanlinesignature.jpg') }}">
                    </div>
                </div>
            </div>
        </div>        
    </body>
</html>

