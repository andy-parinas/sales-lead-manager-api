<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class PaymentSchedule extends JsonResource
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
            'dueDate' => Carbon::parse($this->due_date)->format('d/m/Y'),
            'description' => $this->description,
            'status' => $this->status,
            'amount' => $this->amount,
            'payment' => $this->payment,
            'balance' => $this->balance,
        ];
    }
}
