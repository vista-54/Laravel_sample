<?php

namespace App\Http\Resources\Admin\Client;

use App\Services\Identifier;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'last_transaction' => $this->clientShops()->latest()->first(),
            'client_card' => [
                'unique_id' => \Identifier::generate(Identifier::LOUALTY, $this->id, $this->loyaltyProgram()->first()->card()->first()->id),
                'points_value' => $this->clientLoyaltyProgram->point,
            ],
            'block' => $this->block,
            'frequency_purchase' => $this->client_shops_count,
            'birthday' => $this->birthday,
            'transaction_total' => $this->transaction_total,
        ];
    }
}
