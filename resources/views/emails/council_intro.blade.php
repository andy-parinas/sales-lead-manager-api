
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
                            We are delighted to advise you that your Spanline Home Addition project has
                            now been submitted for statutory approval and we enclose a copy of the plans for
                            your information.
                        </p>
                        <p>
                            While we do not foresee any problems, occasionally the council application
                            requires additional processes, therefore delays do occur. Should this happen,
                            we will be in contact with you as soon as possible.
                        </p>
                        <p>
                            Overleaf we have provided you with some important information relating to
                            your project including details of the Product and Materials specification.
                            Please take the time to read this information, as it will confirm a number of details
                            about your project. If you have any queries at all please contact our Customer
                            Service Department.
                        </p>
                        <p>
                            If you are having concreting or any other structural work completed prior to
                            your Spanline project starting, please advise us once the work is complete, as
                            your project may need to be re-measured.
                        </p>
                        <p>
                            We look forward to contacting you soon to advise that your project has been
                            approved and providing you with material delivery and works schedule details.
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

