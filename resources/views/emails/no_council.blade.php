
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
                    <div class="col-md">{{ $title }}. {{ $firstName }} {{ $lastName }}</div>
                    <div class="col-md">{{ $street }}</div>
                    <div class="col-md">{{ $address }}</div>
                </div>
                <div class="row" style="height:30px"></div>                
                <div class="row">
                    <div class="col-md">
                        <p>Dear {{ $title }}. {{ $lastName }}</p>
                        <p>
                            We are pleased to inform you that your Spanline Home Additions project has
                            been entered into our check measure program.
                        </p>
                        <p>
                            Overleaf we have provided you with some important information relating to
                            your project including details of Product and Materials specifications. Please
                            take the time to read this information, as it will confirm a number of important
                            details about our project.
                        </p>
                        <p>
                            If you have any queries, please do not hesitate to contact our Customer
                            Service Department. If you are satisfied with all the details there is no need to
                            contact us at this point.
                        </p>
                        <p>
                            Spanline Home Additions will be contacting you soon with details regarding
                            the commencement date of your project.
                        </p>
                    </div>
                </div>
                <div class="row" style="height:30px"></div>
                <div class="row">
                    <div class="col-md">Yours faithfully,</div>
                    <div class="col-md">Project Manager</div>
                    <div class="col-md">Spanline Home Additions</div>
                </div>
                <div class="row" style="height:30px"></div>
                <div class="row">
                    <div class="col-md">
                    <img src="{{ asset('images/spanlinesignature.jpg') }}">
                    </div>
                </div>
            </div>
        </div>        
    </body>
</html>

