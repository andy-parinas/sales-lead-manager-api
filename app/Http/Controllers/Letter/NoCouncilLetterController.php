<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\Lead;
use App\User;
use App\SalesContact;
use App\Services\Interfaces\EmailServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class NoCouncilLetterController extends Controller
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

        $to = $salesContact->email;
        $from = 'support@spanline.com.au';

        $subject = "Spanline Home Additions – Project Update";

        $message = view('emails.no_council')->with([
            'dateToday' => $today->format('l, F j, Y'),
            'title' => $salesContact->title,
            'firstName' => $salesContact->first_name,
            'lastName' => $salesContact->last_name,            
            'address' => $address,
            'street' => $street
        ])->render();

        $this->emailService->sendEmail($to, $from, $subject, $message);

        $buildingAuthority->update([
            'no_council_letter_sent' => date("Y-m-d")
        ]);

        $buildingAuthority->refresh();

        Log::info("Unassigned Intro Letter Sent");

        return response(['data' => $buildingAuthority], Response::HTTP_OK);
    }

    public function customSend(Request $request, $leadId)
    {
        $lead = Lead::with(['buildingAuthority', 'salesContact'])->findOrFail($leadId);

        $buildingAuthority = $lead->buildingAuthority;

        if($buildingAuthority == null){
            abort(Response::HTTP_BAD_REQUEST, "Building Authority Is Required");
        }

        $user = Auth::user();
        $today = Carbon::today();

        $file = null;
        $filename = null;
       
        if($request->hasFile('fileForUpload')){
            //VALIDATE FILE
            $data = $this->validate($request, [
                    'fileForUpload' => 'required|file|mimes:pdf,doc,docx,odt,txt|max:5120',
                ]
            );

            $file = $request->file('fileForUpload');
            $filename = $file->getClientOriginalName();            
            $path = $file->storeAs('public/',$filename);
            $file = storage_path('app/public/'.$filename);            
        }
        
        $customContent = isset($request->sentContent) ? $request->sentContent : '' ;
        $street1 = ($lead->salesContact->street1 !==''? $lead->salesContact->street1.', ' : '');
        $street2 = ($lead->salesContact->street2 !==''? $lead->salesContact->street2.', ' : '');
        $locality = ($lead->salesContact->postcode->locality !==''? $lead->salesContact->postcode->locality.', ' : '');
        $state = ($lead->salesContact->postcode->state !==''? $lead->salesContact->postcode->state.', ' : '');
        $pcode = ($lead->salesContact->postcode->pcode !==''? $lead->salesContact->postcode->pcode : '');

        $address = $locality.''.$state.''.$pcode;
        $street = $street1.''.$street2;

        // $to = 'wilsonb@crystaltec.com.au';
        $to = $lead->salesContact->email;
        $customFrom = 'support@spanline.com.au';

        $newCastleEmail = User::where('email', 'like', '%Newcastle%')->get();
        $newCastleEmails = $newCastleEmail->pluck('email')->toArray();
        $checkNewCastleEmail = in_array(auth()->user()->email, $newCastleEmails);
        if($checkNewCastleEmail){
            $customFrom = 'newcastle@spanline.com.au';
        }

        $mackayemail = User::where('email', 'like', '%Mackay%')->get();
        $mackayemails = $mackayemail->pluck('email')->toArray();
        $checkmackayemail = in_array(auth()->user()->email, $mackayemails);
        if($checkmackayemail){
            $customFrom = 'mackay@spanline.com.au';
        }
        
        $from = $customFrom;

        $subject = "Spanline Home Additions – Project Update";

        $message = view('emails.no_council')->with([
            'customContent' => nl2br($customContent),
            'dateToday' => $today->format('l, F j, Y'),
            'title' => $lead->salesContact->title,
            'firstName' => $lead->salesContact->first_name,
            'lastName' => $lead->salesContact->last_name,            
            'address' => $address,
            'street' => $street
        ])->render();

        $this->emailService->sendEmail($to, $from, $subject, $message, $file, $filename);

        $buildingAuthority->update([
            'no_council_letter_sent' => date("Y-m-d")
        ]);

        $buildingAuthority->refresh();

        Log::info("Unassigned Intro Letter Sent");

        return response(['data' => $buildingAuthority], Response::HTTP_OK);
    }
}
