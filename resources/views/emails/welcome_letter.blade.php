
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
                    <div class="col-md">{{ $street1 }} {{ $street2 }},</div>
                    <div class="col-md">{{ $locality }}, {{ $state }}, {{ $pcode }}</div>
                </div>
                <div class="row" style="height:30px"></div>                
                <div class="row">
                    <div class="col-md">
                        <p>Dear {{ $title }}. {{ $lastName }}</p>
                        <p>
                            On behalf of our Spanline Home Additions staff, we are honoured that you
                            have chosen us to provide you with a unique Spanline Home Addition and
                            particularly the lifestyle improvements that the finished project will bring.
                        </p>
                        <p>
                            Spanline is about more than just a building project. It is about serving, satisfying and fulfilling
                            the expectations of our customers. This we look forward to most of all. During the project we will have the
                            opportunity to show you just how seriously we take our Code of Customer Service Excellence.
                        </p>
                        <p>
                            We would like to take this opportunity to assure you that we will be doing our utmost to ensure that everything runs smoothly,
                            but if at any time the unexpected does occur, we will keep you fully informed and aware.
                        </p>
                        <p>
                            Like all of us, we understand that you are very proud of your home and your living environment,
                            and we know that upon completion your new Spanline will add to that pride.
                            We thank you for this opportunity and would like you to know that every one of us looks forward
                            to providing you our Service Excellence.
                        </p>
                    </div>
                </div>
                <div class="row" style="height:30px"></div>
                <div class="row">
                    <div class="col-md">Yours faithfully,</div>
                    <div class="col-md">Franchise Manager</div>
                    <div class="col-md">Spanline Home Additions {{ $franchiseName }}</div>
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

