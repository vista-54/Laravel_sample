<?php

namespace App\Http\Resources\Admin\ClientOffer;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientOfferResource extends JsonResource
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
            'loyalty_program_id' => $this->loyalty_program_id,
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'points_cost' => $this->points_cost,
            'customer_limit' => $this->customer_limit,
            'availability_count' => $this->availability_count,
            'notify' => $this->notify,
            'offer_card' => new ClientOfferCardResource($this->offer_card),
        ];
    }
}
