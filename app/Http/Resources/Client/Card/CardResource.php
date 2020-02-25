<?php

namespace App\Http\Resources\Client\Card;

use App\Http\Resources\Client\Offer\ClientOfferResource;
use App\Http\Resources\Collection;
use App\Models\Offer;
use App\Services\Identifier;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
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
            'loyalty_program_id' => $this->loyalty_program_id,
            'background_color' => $this->background_color,
            'background_main_color' => $this->background_main_color,
            'foreground_color' => $this->foreground_color,
            'label_color' => $this->label_color,
            'points_head' => $this->points_head,
            'points_value' => auth()->user()->clientLoyaltyProgram->point,
            'customer_head' => $this->customer_head,
            'customer_value' => auth()->user()->fullname,
            'flip_head' => $this->flip_head,
            'flip_value' => $this->flip_value,
            'loyalty_profile' => $this->loyalty_profile,
            'loyalty_offers' => $this->loyalty_offers,
            'loyalty_contact' => $this->loyalty_contact,
            'loyalty_terms' => $this->loyalty_terms,
            'loyalty_terms_value' => $this->loyalty_terms_value,
            'loyalty_message' => $this->loyalty_message,
            'icon' => $this->icon,
            'background_image' => $this->background_image,
            'customer_id' => \Barcode::generate(\Identifier::generate(Identifier::LOUALTY, auth()->user()->id, $this->id)),
            'stamps' => $this->stamps,
            'offers' => new Collection(ClientOfferResource::collection($this->loyaltyProgram->offers()->where('status', Offer::ACTIVE)->get())),
            'unique_id' => 'C' . '-' . auth()->user()->id . '-' . $this->id
//            'unique_id' => \Identifier::generate(Identifier::LOUALTY, auth()->user()->id, $this->id)
        ];
    }
}
