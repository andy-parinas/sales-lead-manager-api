<?php

namespace App\Http\Controllers\Letter;

use App\CustomerReview;
use App\Http\Controllers\Controller;
use App\Lead;
use App\SalesContact;
use App\Services\Interfaces\EmailServiceInterface;
use Illuminate\Http\Request;
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

        $to = $salesContact->email;
        $from = $user->email;
        $subject = "Spanline - Maintenance Letter";

        $message = "<p>Dear {$salesContact->title}. {$salesContact->last_name},  </p>" .
            "<p>Thank you for choosing Spanline for your Home Addition.</p>".
            "<p>We have received your warranty registration at the Spanline Australia office and we have logged its return
                in our system for the duration of the warranty offered to you. With most things under warranty
                you are required to provide general care and maintenance, and your new Home Addition is no different.</p>" .
            "<p>You will have received with your Spanline Brand Product Warranty booklet a “care and maintenance” card – like the one attached.
                This card clearly advises you of your obligations in the care and maintenance and identifies the type of area that your Spanline
                Home Addition has been constructed in to ensure you schedule your care and maintenance to suit.</p>" .
            "<p>It is important that should in the rare instance you need to make a warranty claim on a Spanline Manufactured or
                Spanline Branded product* that you can demonstrate you have provided the appropriate care and maintenance to your Home Addition.</p>" .

            "<p>If you did not receive your SpanPak which includes a Spanline Branded Product Warranty Book,
                a bottle of Blue Clean and a Care & Maintenance card, please contact the Spanline franchise you contracted with to obtain.</p>";


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
