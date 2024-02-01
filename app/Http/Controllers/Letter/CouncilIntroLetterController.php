<?php

namespace App\Http\Controllers\Letter;

use App\Http\Controllers\Controller;
use App\User;
use App\Lead;
use App\SalesContact;
use App\Services\Interfaces\EmailServiceInterface;
use App\Repositories\Interfaces\CustomFromEmailInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CouncilIntroLetterController extends Controller
{
    protected $emailService;
    protected $customFromEmailRepository;

    public function __construct(
        EmailServiceInterface $emailService,
        CustomFromEmailInterface $customFromEmailRepository
    ){
        $this->middleware("auth:sanctum");
        $this->emailService = $emailService;
        $this->customFromEmailRepository = $customFromEmailRepository;
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

        $message = view('emails.council_intro')->with([
            'dateToday' => $today->format('l, F j, Y'),
            'title' => $salesContact->title,
            'firstName' => $salesContact->first_name,
            'lastName' => $salesContact->last_name,            
            'address' => $address,
            'street' => $street
        ])->render();

        $this->emailService->sendEmail($to, $from, $subject, $message);

        $buildingAuthority->update([
            'intro_council_letter_sent' => date("Y-m-d")
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

        if($lead->salesContact->email == "noemail@email.com"){
            abort(Response::HTTP_BAD_REQUEST, "Recipient Email Is Invalid");
        }

        // $to = 'wilsonb@crystaltec.com.au';
        $to = $lead->salesContact->email;
        $from = $this->customFromEmailRepository->emailFrom(Auth::user()->username);
        
        $subject = "Spanline Home Additions – Project Update";

        $message = view('emails.council_intro')->with([
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
            'intro_council_letter_sent' => date("Y-m-d")
        ]);

        $buildingAuthority->refresh();

        Log::info("Unassigned Intro Letter Sent");

        return response(['data' => $buildingAuthority], Response::HTTP_OK);
    }
}
