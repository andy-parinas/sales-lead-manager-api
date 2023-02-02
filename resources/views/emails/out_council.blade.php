
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
                            We are pleased to inform you that your Spanline Home Additions Project has
                            been approved. Now that approval has been received we have commenced
                            preparing your project for construction.
                        </p>
                        <p>
                            We have taken the liberty of outlining the expected works schedule overleaf
                            and where necessary noted any special considerations. Please take a
                            moment to run through the expected works schedule and if there are any
                            points you would like to raise on this schedule, please do not hesitate to call.
                            If you are satisfied that everything is in order you do not need to phone, as we
                            will contact you to advise of construction commencement details.
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

