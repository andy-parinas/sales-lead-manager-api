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

class OutOfCouncilLetterController extends Controller
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

        $buildingAuthority = $lead->buildingAuthority;

        if($buildingAuthority == null){
            abort(Response::HTTP_BAD_REQUEST, "Building Authority Is Required");
        }

        $user = Auth::user();
        $today = Carbon::today();
        
        $street1 = ($salesContact->street1 !==''? $salesContact->street1.', ' : '');
        $street2 = ($salesContact->street2 !==''? $salesContact->street2.', ' : '');
        $locality = ($salesContact->postcode->locality !==''? $salesContact->postcode->locality.', ' : '');
        $state = ($salesContact->postcode->state !==''? $salesContact->postcode->state.', ' : '');
        $pcode = ($salesContact->postcode->pcode !==''? $salesContact->postcode->pcode : '');

        $address = $locality.''.$state.''.$pcode;
        $street = $street1.''.$street2;

        $to =  $salesContact->email;
        $from = 'support@spanline.com.au';

        $subject = "Spanline Home Additions – Project Update";

        $message = view('emails.out_council')->with([
            'dateToday' => $today->format('l, F j, Y'),
            'title' => $salesContact->title,
            'firstName' => $salesContact->first_name,
            'lastName' => $salesContact->last_name,            
            'address' => $address,
            'street' => $street
        ])->render();

        $this->emailService->sendEmail($to, $from, $subject, $message);

        $buildingAuthority->update([
            'out_of_council_letter_sent' => date("Y-m-d")
        ]);

        $buildingAuthority->refresh();

        Log::info("Unassigned Intro Letter Sent");

        return response(['data' => $buildingAuthority], Response::HTTP_OK);
    }
}
