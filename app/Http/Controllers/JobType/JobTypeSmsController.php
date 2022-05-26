<?php

namespace App\Http\Controllers\JobType;

use App\Http\Controllers\Controller;
use App\Lead;
use App\SalesContact;
use App\SalesStaff;
use App\Services\Interfaces\SmsServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class JobTypeSmsController extends Controller
{

    protected $smsService;

    public function __construct(SmsServiceInterface $smsService)
    {
        $this->middleware('auth:sanctum');
        $this->smsService = $smsService;

    }


    public function send(Request $request, $leadId, $salesStaffId)
    {

        try {



            $lead = Lead::findOrFail($leadId);
            $salesContact = $lead->salesContact;
            $salesStaff = SalesStaff::findOrFail($salesStaffId);

            $leadDate = Carbon::parse($lead->lead_date);
            $postcode = $salesContact->postcode;

            $message = "A new Sales Lead has been assigned to you\n";
            $message = $message. "LN:{$lead->lead_number}\n";
            $message = $message. "Name: {$salesContact->first_name} {$salesContact->last_name} \n";
            $message = $message . "Contact Number: {$salesContact->contact_number} \n";
            $message = $message . "<p>Address: <strong>{$salesContact->street1} {$salesContact->street2}, {$postcode->locality}, {$postcode->state} {$postcode->pcode}</strong></p>" .
            $message = $message . "Email: {$salesContact->email}\n";
            $message = $message . "Lead Date: {$leadDate->toDateString()} \n";
            $message = $message . "Product: {$lead->jobType->product->name} \n";
            $message = $message . "Description: {$lead->jobType->description} \n";
            $message = $message . "Lead Received Via: {$lead->received_via} \n";
            $message = $message . "Lead Source: {$lead->leadSource->name} \n";

            $jobType = $lead->jobType;

            $jobType->update([
                'sms_sent_to_design_advisor' => date("Y-m-d")
            ]);

           $this->smsService->sendSms($salesStaff->contact_number, $message);

            return response(['data' => $jobType->sms_sent_to_design_advisor], Response::HTTP_OK);

        }catch (\Exception $exception)
        {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, "Error Sending Message");
        }



    }
}
