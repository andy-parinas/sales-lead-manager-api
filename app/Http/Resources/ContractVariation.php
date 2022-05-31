<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ContractVariation extends JsonResource
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
            'variationDate' => Carbon::parse($this->variation_date)->format('d/m/Y'),
            'description' => $this->description,
            'amount' => $this->amount,
        ];
    }
}
