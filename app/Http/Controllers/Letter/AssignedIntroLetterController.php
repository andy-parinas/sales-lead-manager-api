<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Lead;
use App\SalesContact;
use App\Services\Interfaces\EmailServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AssignedIntroLetterController extends Controller
{
    protected $emailService;

    public function __construct(EmailServiceInterface $emailService){
        $this->middleware("auth:sanctum");
        $this->emailService = $emailService;
    }


    public function send(Request $request, $leadid, $salesContactId)
    {
        $salesContact = SalesContact::with('postcode')->findOrFail($salesContactId);
        $lead = Lead::findOrFail($leadid);
        $designAdvisor = $lead->jobType->salesStaff;

        $user = Auth::user();
        $today = Carbon::today();

        $to = $salesContact->email;
        $from = $user->email;


        $subject = "Spanline Home Additions Design Consultation";

        $message = "<p> {$today->toFormattedDateString()} </p> <br/> <br/>" .
            "<div>{$salesContact->title}. {$salesContact->frist_name} {$salesContact->last_name} </div>" .
            "<div>{$salesContact->street1}, {$salesContact->street2}</div>" .
            "<div>{$salesContact->postcode->locality}, {$salesContact->postcode->state}, {$salesContact->postcode->pcode}</div> <br/> <br/>" .
            "<p>Dear {$salesContact->title}. {$salesContact->last_name},  </p>" .
            "<p>Thank you for considering Spanline Home Additions {$lead->franchise->name} for your new home addition project.<p>" .
            "<p>We often find people are unsure what to expect from a Design Consultation,
                so as the first step in delivering our Customer Service Standards, this letter outlines the consultation process and
                full extent of our service commitment to you.</p>".
            "<p>You will soon, or may have been contacted by our Design Advisor, {$designAdvisor->fullName}.
                As a fully accredited design specialist, {$designAdvisor->first_name} is qualified to
                provide extensive advice and assistance to you. They will arrive promptly at your agreed appointment time and will identify
                themselves by producing their personal Spanline photo ID card.
                These cards are only issued to accredited Spanline Design Advisors.
                You are assured that {$designAdvisor->first_name} will listen to all your requirements
                and needs and will take into account your full expectations before offering any advice or
                design ideas for your consideration.</p>" .
            "<p>At Spanline, we value honesty and integrity, so {$designAdvisor->first_name}
                will explain to you exactly what the Spanline product is, how it performs and what
                you can expect from your new Spanline Home Addition. Importantly, Spanline's exclusive National Customer
                Service Code of Excellence will also be explained. This code will mean a lot to you should you
                decide to have your home addition project undertaken by Spanline.
                When delivering your Spanline solution, {$designAdvisor->first_name}
                will consider all possible options to ensure the project meets the level of investment you are anticipating.</p>" .
            "<p>Spanline Home Additions is committed to providing quality services to you and our privacy policy
                (https://spanline.com.au/about-us/our-policies/) outlines how we manage your personal information.
                We have adopted the Australian Privacy Principles (APPs) contained in the Privacy Act 1988 (Cth)
                (the Privacy Act). The APPs govern the way in which we collect, use, disclose, store, secure and
                dispose of your personal information. A copy of the APPs may be obtained from The Office of the
                Federal Privacy Commissioner at www.privacy.gov.au.</p>".
            "<p>We value your interest in Spanline and look forward to providing you with the very best of service.
                Please visit the Projects page on our website to view some of our recent happy customers’
                completed home additions (https://spanline.com.au/project/). If there is anything we can do,
                now or after your design consultation and proposal, please contact one of our friendly
                team and they will be happy to assist you. </p>" .
            "<p>Thank you again for giving Spanline the opportunity to meet with you and
                advise you on your home addition project.</p><br/> <br/>" .
            "<p>Regards,</p> <br/>" .
            "<div>Franchise Manager</div>" .
            "<div>Spanline Home Additions {$lead->franchise->name}</div>";

        $this->emailService->sendEmail($to, $from, $subject, $message);
        Log::info("Assigned Intro Letter Sent {$to}");

        $lead->update([
            'assigned_intro_sent' => date("Y-m-d")
        ]);

        if ($salesContact->email2 !== null){
            $to2 = $salesContact->email2;
            $this->emailService->sendEmail($to2, $from, $subject, $message);
            Log::info("Assigned Intro Letter Sent {$to2}");
        }

        $lead->refresh();



        return response(['data' => $lead], Response::HTTP_OK);
    }
}
