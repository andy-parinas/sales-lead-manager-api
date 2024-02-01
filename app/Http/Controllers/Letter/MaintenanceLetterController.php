<?php

namespace App\Http\Controllers\Letter;

use App\CustomerReview;
use App\Http\Controllers\Controller;
use App\User;
use App\Lead;
use App\SalesContact;
use App\Services\Interfaces\EmailServiceInterface;
use App\Repositories\Interfaces\CustomFromEmailInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceLetterController extends Controller
{
    private $emailService;
    protected $customFromEmailRepository;

    public function __construct(
        EmailServiceInterface $emailService,
        CustomFromEmailInterface $customFromEmailRepository
    )
    {
        $this->middleware('auth:sanctum');
        $this->emailService = $emailService;
        $this->customFromEmailRepository = $customFromEmailRepository;

    }
    
    public function send(Request $request, $customerReviewId, $salesContactId)
    {
        $salesContact = SalesContact::findOrFail($salesContactId);
        $customerReview = CustomerReview::findOrFail($customerReviewId);
        
        $user = Auth::user();
        $today = Carbon::today();

        $to = $salesContact->email;
        $from = 'support@spanline.com.au';
        $subject = "Spanline - Maintenance Letter";

        $message = view('emails.maintenance_letter')->with([
            'dateToday' => $today->toFormattedDateString(),
            'title' => $salesContact->title,
            'lastName' => $salesContact->last_name
        ])->render();

        $letter =  'attachments/warranty_care_maintenance_letter.pdf';

        if(!Storage::disk('local')->exists($letter)){
            abort(Response::HTTP_BAD_REQUEST, "attachment does not exist");
        }

        $attachment = Storage::path($letter);

        try {

            $this->emailService->sendEmail($to, $from, $subject, $message, $attachment);

            $customerReview->update([
                'maintenance_letter_sent' => date("Y-m-d")
            ]);

            $customerReview->refresh();

            return response(['data' => $customerReview], Response::HTTP_OK);

        }catch (\Exception $exception){

            abort(Response::HTTP_BAD_REQUEST, "Error in Sending Email");

        }
        
    }

    public function customSend(Request $request, $leadId)
    {
        $lead = Lead::with(['salesContact', 'customerReview'])->findOrFail($leadId);
        
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

        if($lead->salesContact->email == "noemail@email.com"){
            abort(Response::HTTP_BAD_REQUEST, "Recipient Email Is Invalid");
        }

        // $to = 'wilsonb@crystaltec.com.au';
        $to = $lead->salesContact->email;
        $from = $this->customFromEmailRepository->emailFrom(Auth::user()->username);
        
        $subject = "Spanline - Maintenance Letter";

        $message = view('emails.maintenance_letter')->with([
            'customContent' => nl2br($customContent),
            'dateToday' => $today->toFormattedDateString(),
            'title' => $lead->salesContact->title,
            'lastName' => $lead->salesContact->last_name
        ])->render();

        $this->emailService->sendEmail($to, $from, $subject, $message, $file, $filename);

        $lead->customerReview->update([
            'maintenance_letter_sent' => date("Y-m-d")
        ]);

        $lead->customerReview->refresh();

        return response(['data' => $lead->customerReview], Response::HTTP_OK);        
    }
}
