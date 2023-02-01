
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
                    <div class="col-md">{{ $title }}. {{ $firstName }} {{ $lastName }}</div>
                    <div class="col-md">{{ $street1 }} {{ $street2 }},</div>
                    <div class="col-md">{{ $locality }}, {{ $state }}, {{ $pcode }}</div>
                </div>
                <div class="row" style="height:30px"></div>                
                <div class="row">
                    <div class="col-md">
                        <p>Dear {{ $title }}. {{ $lastName }}</p>
                        <p>Thank you for considering Spanline Home Additions {{ $franchiseName }} for your new home addition project.</p>
                        <p>
                            We often find people are unsure what to expect from a Design Consultation, so as the first step in delivering
                            our Customer Service Standards, this letter outlines the consultation process and full extent of our service commitment to you
                        </p>
                        <p>
                            You will soon be contacted by one of our fully accredited specialist Design Advisors who
                            will be able to provide extensive advice and assistance to you.
                        </p>
                        <p>
                            You are assured that our Design Advisor will arrive promptly at your agreed appointment time and will
                            identify themselves by producing their personal Spanline photo ID card. These cards are only
                            issued to accredited Spanline Design Advisors. You are assured that your Design Advisor will
                            listen to all your requirements and needs and will take into account your full expectations
                            before offering any advice or design ideas for your consideration.
                        </p>
                        <p>
                            At Spanline, we value honesty and integrity, so your Design Advisor will explain to you exactly
                            what the Spanline product is, how it performs and what you can expect from your new Spanline Home Addition.
                            Importantly, Spanline's exclusive National Customer Service Code of Excellence will also be explained.
                            This code will mean a lot to you should you decide to have your home addition project undertaken by Spanline.
                            When delivering your Spanline solution, your Design Advisor will consider all possible options to ensure the
                            project meets the level of investment you are anticipating.
                        </p>
                        <p> 
                            Spanline Home Additions is committed to providing quality services to you and our privacy policy
                            (https://spanline.com.au/about-us/our-policies/) outlines how we manage your personal information.
                            We have adopted the Australian Privacy Principles (APPs) contained in the Privacy Act 1988 (Cth) (the Privacy Act).
                            The APPs govern the way in which we collect, use, disclose, store, secure and dispose of your personal information.
                            A copy of the APPs may be obtained from The Office of the Federal Privacy Commissioner at www.privacy.gov.au.
                        </p>
                        <p>
                            We value your interest in Spanline and look forward to providing you with the very best of service.
                            Please visit the Projects page on our website to view some of our recent happy customers’
                            completed home additions (https://spanline.com.au/project/). If there is anything we can do,
                            now or after your design consultation and proposal, please contact one of our friendly
                            team and they will be happy to assist you.
                        </p>
                        <p>
                            Thank you again for giving Spanline the opportunity to meet with you and advise you on your home addition project.
                        </p>
                    </div>
                </div>
                <div class="row" style="height:30px"></div>
                <div class="row">
                    <div class="col-md">Regards,</div>
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

