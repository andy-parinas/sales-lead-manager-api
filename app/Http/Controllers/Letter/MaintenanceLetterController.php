<?php

namespace App\Http\Controllers\Letter;

use App\CustomerReview;
use App\Http\Controllers\Controller;
use App\Lead;
use App\SalesContact;
use App\Services\Interfaces\EmailServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceLetterController extends Controller
{
    private $emailService;

    public function __construct(EmailServiceInterface $emailService)
    {
        $this->middleware('auth:sanctum');
        $this->emailService = $emailService;

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
}
