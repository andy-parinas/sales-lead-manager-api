<?php

namespace App\Http\Controllers\JobType;

use App\Http\Controllers\Controller;
use App\Lead;
use App\SalesStaff;
use App\Services\Interfaces\EmailServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class JobTypeEmailController extends Controller
{

    protected $emailService;

    public function __construct(EmailServiceInterface $emailService)
    {
        $this->middleware('auth:sanctum');
        $this->emailService = $emailService;
    }


    public function send(Request $request, $leadId, $salesStaffId)
    {
        $lead = Lead::findOrFail($leadId);
        $salesContact = $lead->salesContact;
        $salesStaff = SalesStaff::findOrFail($salesStaffId);
        $jobType = $lead->jobType;

        $user = Auth::user();
        $today = Carbon::today();

        $leadDate = Carbon::parse($lead->lead_date);
        $postcode = $salesContact->postcode;
        
        $fullName = $salesContact->first_name.' '.$salesContact->last_name;

        $street1 = ($salesContact->street1 !==''? $salesContact->street1.', ' : '');
        $street2 = ($salesContact->street2 !==''? $salesContact->street2.', ' : '');
        $locality = ($postcode->locality !==''? $postcode->locality.', ' : '');
        $state = ($postcode->state !==''? $postcode->state.', ' : '');
        $pcode = ($postcode->pcode !==''? $postcode->pcode : '');

        $address = $street1.''.$street2.''.$locality.''.$state.''.$pcode;
        
        $to = $salesStaff->email;
        $from = 'support@spanline.com.au';

        $subject = "New Sales Lead Assigned: {$lead->lead_number}";

        $message = view('emails.job_type')->with([
            'dateToday' => $today->toFormattedDateString(),
            'leadNumber' => $lead->lead_number,            
            'fullName' => $fullName,
            'contactNumber' => $salesContact->contact_number,
            'address' => $address,            
            'email' => $salesContact->email,
            'leadDate' => $leadDate->toDateString(),
            'product' => $lead->jobType->product->name,
            'description' => $lead->jobType->description,
            'leadReceivedVia' => $lead->received_via,
            'leadSource' => $lead->leadSource->name
        ])->render();

        $this->emailService->sendEmail($to, $from, $subject, $message);
        
        $jobType->update([
            'email_sent_to_design_advisor' => date("Y-m-d")
        ]);

        return response(['data' => $jobType], Response::HTTP_OK);

    }
}
