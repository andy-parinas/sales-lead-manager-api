
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
                        {!! $customContent !!}
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

