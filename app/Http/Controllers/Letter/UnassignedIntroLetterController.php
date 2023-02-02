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

class UnassignedIntroLetterController extends Controller
{

    protected $emailService;

    public function __construct(EmailServiceInterface $emailService){
        $this->middleware("auth:sanctum");
        $this->emailService = $emailService;
    }

    public function send(Request $request,$leadid, $salesContactId)
    {
        $salesContact = SalesContact::with('postcode')->findOrFail($salesContactId);
        $lead = Lead::findOrFail($leadid);

        $user = Auth::user();
        $today = Carbon::today();

        $street1 = ($salesContact->street1 !==''? $salesContact->street1.', ' : '');
        $street2 = ($salesContact->street2 !==''? $salesContact->street2.', ' : '');
        $locality = ($salesContact->postcode->locality !==''? $salesContact->postcode->locality.', ' : '');
        $state = ($salesContact->postcode->state !==''? $salesContact->postcode->state.', ' : '');
        $pcode = ($salesContact->postcode->pcode !==''? $salesContact->postcode->pcode : '');

        $address = $locality.''.$state.''.$pcode;
        $street = $street1.''.$street2;

        $to = $salesContact->email;
        $from = 'support@spanline.com.au';

        $subject = "Spanline Home Additions Design Consultation";
        
        $message = view('emails.intro_unassign')->with([
            'dateToday' => $today->toFormattedDateString(),
            'title' => $salesContact->title,
            'firstName' => $salesContact->first_name,
            'lastName' => $salesContact->last_name,
            'street' => $street,
            'address' => $address,
            'franchiseName' => $lead->franchise->name
        ])->render();
        
        $this->emailService->sendEmail($to, $from, $subject, $message);
        Log::info("Unassigned Intro Letter Sent {$to}");

        $lead->update([
            'unassigned_intro_sent' => date("Y-m-d")
        ]);

        if ($salesContact->email2 !== null){
            $to2 = $salesContact->email2;
            $this->emailService->sendEmail($to2, $from, $subject, $message);
            Log::info("Unassigned Intro Letter Sent {$to2}");
        }

        $lead->refresh();
        
        return response(['data' => $lead], Response::HTTP_OK);
    }
}
