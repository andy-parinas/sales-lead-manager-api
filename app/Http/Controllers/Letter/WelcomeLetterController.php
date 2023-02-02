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

class WelcomeLetterController extends Controller
{
    protected $emailService;

    public function __construct(EmailServiceInterface $emailService){
        $this->middleware("auth:sanctum");
        $this->emailService = $emailService;
    }


    public function send(Request $request, $leadId)
    {
        $lead = Lead::findOrFail($leadId);

        $salesContact = $lead->salesContact;

        $contract = $lead->contract;

        if($contract == null){
            abort(Response::HTTP_BAD_REQUEST, "Lead must have contract.");
        }

        $user = Auth::user();
        $today = Carbon::today();

        $to = $salesContact->email;
        $from = 'support@spanline.com.au';

        $subject = "Welcome to Spanline Home Additions";

        $message = view('emails.welcome_letter')->with([
            'dateToday' => $today->format('l, F j, Y'),
            'title' => $salesContact->title,
            'firstName' => $salesContact->first_name,
            'lastName' => $salesContact->last_name,
            'street1' => $salesContact->street1,
            'street2' => $salesContact->street2,
            'locality' => $salesContact->postcode->locality,
            'state' => $salesContact->postcode->state,
            'pcode' => $salesContact->postcode->pcode,
            'franchiseName' => $lead->franchise->name
        ])->render();

        $this->emailService->sendEmail($to, $from, $subject, $message);

        $contract->update([
            'welcome_letter_sent' => date("Y-m-d")
        ]);

        $contract->refresh();

        Log::info("Welcome Letter Sent {$contract->welcome_letter_sent}");

        return response(['data' => $contract], Response::HTTP_OK);
    }
}
