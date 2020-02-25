<?php

namespace App\Http\Resources\Admin\Client;

use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        unset($data['lifetime_value']);
        return $data + [
                'client_card' => new CardResource($this->loyaltyProgram()->first()->card()->first(), $this->id),
                'available_offers' => Offer::where('status', Offer::ACTIVE)->get(),
                'last_transaction' => $this->clientShops()->latest()->first(),
                'lifetime_value' => $this->clientShops()->sum('amount') . ' ' . $this->user->loyaltyProgram->currency
            ];
    }
}
