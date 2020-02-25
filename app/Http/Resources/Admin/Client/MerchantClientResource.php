<?php

namespace App\Http\Resources\Admin\Client;

use App\Services\Identifier;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'phone' => $this->phone,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'address' => $this->address,
            'timezone' => $this->timezone,
            'code' => $this->code,
            'social' => $this->social,
            'block' => $this->block,
            'race' => $this->race,
            'lifetime_value' => $this->lifetime_value,
            'birthday' => $this->birthday,
            'country_code' => $this->country_code,
            'points' => $this->points,
            'transaction' => $this->transaction,
            'currency' => $this->currency,
            'last_transaction' => $this->clientShops()->latest()->first(),
            'client_card' => [
                'unique_id' => \Identifier::generate(Identifier::LOUALTY, $this->id, $this->loyaltyProgram()->first()->card()->first()->id),
                'points_value' => $this->clientLoyaltyProgram->point,
            ],
            'frequency_purchase' => $this->client_shops_count,
            'transaction_total' => $this->transaction_total,
            'active_offer' => $this->offer_status,
            'app_url' => $this->app_url,
            'device_type' => $this->device_type,
            'last_log' => $this->logs()->latest()->first()
        ];
    }
}
