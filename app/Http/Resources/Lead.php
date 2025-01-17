<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Lead extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
           'details' => [
               'id' => $this->id,
               'leadNumber' => $this->lead_number,
               'referenceNumber' => $this->reference_number,
               'leadDate' => $this->lead_date,
               'postcodeStatus' => $this->postcode_status,
               'franchiseId' => $this->franchise->id,
               'franchiseNumber' => $this->franchise->franchise_number,
               'leadSourceId' =>$this->leadSource->id,
               'leadSource' => $this->leadSource->name,
               'salesContactId' => $this->salesContact->id,
               'title' => $this->salesContact->title,
               'firstName' => $this->salesContact->first_name,
               'lastName' => $this->salesContact->last_name,
               'email' => $this->salesContact->email,
               'email2' => $this->salesContact->email2,
               'contactNumber' => $this->salesContact->contact_number,
               'street1' => $this->salesContact->street1,
               'street2' => $this->salesContact->street2,
               'suburb' => $this->salesContact->suburb,
               'state' => $this->salesContact->state,
               'postcode' => $this->salesContact->postcode,
               'customerType' => $this->salesContact->customer_type,
               'status' => $this->salesContact->status,
               'created_at' => $this->created_at,
               'receivedVia' => $this->received_via,
               'unAssignedIntroSent' => $this->unassigned_intro_sent,
               'assignedIntroSent' => $this->assigned_intro_sent
           ],
            'jobType' => $this->jobType ? [
                'id' => $this->jobType->id,
                'leadId' => $this->id,
                'takenBy' => $this->jobType->taken_by,
                'dateAllocated' => $this->jobType->date_allocated,
                'description' => $this->jobType->description,
                'productId' => $this->jobType->product->id,
                'product' => $this->jobType->product->name,
                'designAssessorId' => $this->jobType->salesStaff->id,
                'designAssessor' => $this->jobType->salesStaff->full_name,
                'designAssessorEmail' => $this->jobType->salesStaff->email,
                'designAssessorContactNumber' => $this->jobType->salesStaff->contact_number,
                'emailSentToDesignAdvisor' => $this->jobType->email_sent_to_design_advisor,
                'smsSentToDesignAdvisor' => $this->jobType->sms_sent_to_design_advisor
            ] : null,
            'appointment' => $this->appointment ? [
                'id' => $this->appointment->id,
                'appointmentDate' => $this->appointment->date,
                'appointmentTime' => $this->appointment->time,
                'date' => $this->appointment->appointment_date,
                'notes' => $this->appointment->appointment_notes,
                'quotedPrice' => $this->appointment->quoted_price,
                'outcome' => $this->appointment->outcome,
                'comments' => $this->appointment->comments,
                'followUp' => $this->appointment->followup_date,
                'followUpDate' => $this->appointment->FollowUpDateString,
                'followUpTime' => $this->appointment->followUpTime,
                'withContract' => $this->contract ? true : false,
                'customerTouchPoint' => $this->appointment->customer_touch_point
            ] : null
        ];
    }
}
