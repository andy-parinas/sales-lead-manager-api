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

        $message = view('emails.intro_assign')->with([
            'dateToday' => $today->toFormattedDateString(),
            'title' => $salesContact->title,
            'firstName' => $salesContact->first_name,
            'lastName' => $salesContact->last_name,
            'street' => $street,
            'address' => $address,
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

    /**
     * Lead data and connected tables
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function leadData($leadId)
    {
        $lead = Lead::with(['franchise', 'salesContact', 'jobType' => function($query){
            $query->with(['salesStaff']);
        }])->findOrFail($leadId);

        return response(['data' => $lead], Response::HTTP_OK);
    }

    /**
     * Send Customed Intro Letter
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customSend(Request $request, $leadId)
    {
        $lead = Lead::with(['franchise', 'salesContact', 'jobType' => function($query){
            $query->with(['salesStaff']);
        }])->findOrFail($leadId);

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
        $franchiseName = ($lead->franchise->name !==''? $lead->franchise->name : '');
        $designAdviserFullname = (isset($lead->jobType->salesStaff->fullName)? $lead->jobType->salesStaff->fullName : '');
        $designAdviserFname = (isset($lead->jobType->salesStaff->first_name)? $lead->jobType->salesStaff->first_name : '');
        
        $customContent1 = str_replace("*franchiseName", $franchiseName, $customContent);
        $customContent2 = str_replace("*designAdviserFullname", $designAdviserFullname, $customContent1);
        $customContent3 = str_replace("*designAdviserFname", $designAdviserFname, $customContent2);
        
        $address = $locality.''.$state.''.$pcode;
        $street = $street1.''.$street2;
        
        // $to = 'wilsonb@crystaltec.com.au';
        $to = $salesContact->email;
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
        
        $subject = "Spanline Home Additions Design Consultation";

        $message = view('emails.intro_assign')->with([
            'customContent' => nl2br($customContent3),
            'dateToday' => $today->toFormattedDateString(),
            'title' => $lead->salesContact->title,
            'firstName' => $lead->salesContact->first_name,
            'lastName' => $lead->salesContact->last_name,
            'street' => $street,
            'address' => $address,
            'franchiseName' => $franchiseName,
            'designAdviserFullname' => $designAdviserFullname,
            'designAdviserFname' => $designAdviserFname
        ])->render();
        
        $this->emailService->sendEmail($to, $from, $subject, $message, $file, $filename);
        // $this->emailService->sendEmail($to, $from, $subject, $message);
        
        Log::info("Unassigned Intro Letter Sent {$to}");

        $lead->update([
            'unassigned_intro_sent' => date("Y-m-d")
        ]);

        if ($lead->salesContact->email2 !== null){
            $to2 = $lead->salesContact->email2;
            $this->emailService->sendEmail($to2, $from, $subject, $message, $file, $filename);
            Log::info("Unassigned Intro Letter Sent {$to2}");
        }

        $lead->refresh();
        
        return response(['data' => $lead], Response::HTTP_OK);
    }
}
