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
        $from = 'support@spanline.com.au';

        $subject = "Spanline Home Additions Design Consultation";

        $message = view('emails.intro_assign')->with([
            'dateToday' => $today->toFormattedDateString(),
            'title' => $salesContact->title,
            'firstName' => $salesContact->frist_name,
            'lastName' => $salesContact->last_name,
            'street1' => $salesContact->street1,
            'street2' => $salesContact->street2,
            'locality' => $salesContact->postcode->locality,
            'state' => $salesContact->postcode->state,
            'pcode' => $salesContact->postcode->pcode,
            'franchiseName' => $lead->franchise->name,
            'designAdviserFullname' => $designAdvisor->fullName,
            'designAdviserFname' => $designAdvisor->first_name
        ])->render();

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

    public function asssignHtmlEmail()
    {
        return view('emails.intro_unassign');
    }
}
