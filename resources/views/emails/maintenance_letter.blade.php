
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
                <div class="row" style="height:50px"></div>             
                <div class="row">
                    <div class="col-md">
                        <p>Dear {{ $title }}. {{ $lastName }}</p>
                        <p>Thank you for choosing Spanline for your Home Addition.</p>
                        <p>
                            We have received your warranty registration at the Spanline Australia office and we have logged its return
                            in our system for the duration of the warranty offered to you. With most things under warranty
                            you are required to provide general care and maintenance, and your new Home Addition is no different.
                        </p>
                        <p>
                            You will have received with your Spanline Brand Product Warranty booklet a “care and maintenance” card – like the one attached.
                            This card clearly advises you of your obligations in the care and maintenance and identifies the type of area that your Spanline
                            Home Addition has been constructed in to ensure you schedule your care and maintenance to suit.
                        </p>
                        <p>
                            It is important that should in the rare instance you need to make a warranty claim on a Spanline Manufactured or
                            Spanline Branded product* that you can demonstrate you have provided the appropriate care and maintenance to your Home Addition.
                        </p>
                        <p>
                            If you did not receive your SpanPak which includes a Spanline Branded Product Warranty Book,
                            a bottle of Blue Clean and a Care & Maintenance card, please contact the Spanline franchise you contracted with to obtain.
                        </p>
                    </div>
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

