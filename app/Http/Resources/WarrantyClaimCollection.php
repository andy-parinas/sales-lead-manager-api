<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WarrantyClaimCollection extends JsonResource
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
            'id' => $this->id,
            'dateComplaint' => $this->date_complaint,
            'complaintReceived' => $this->complaint_received,
            'complaintType' => $this->complaint_type,
            'homeAdditionType' => $this->home_addition_type,
            'complaintDescription' => $this->complaint_description,
            'contactedFranchise' => $this->contacted_franchise,
            'status' => $this->status
        ];
    }
}
