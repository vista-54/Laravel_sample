<?php

namespace App\Http\Resources\Client\Offer;

use App\Services\Identifier;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientOfferCardResource extends JsonResource
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
            'offer_id' => $this->offer_id,
            'background_color' => $this->background_color,
            'background_main_color' => $this->background_main_color,
            'foreground_color' => $this->foreground_color,
            'label_color' => $this->label_color,
            'points_head' => $this->points_head,
            'points_value' => auth()->user()->clientLoyaltyProgram->point,
            'offer_head' => $this->offer_head,
            'offer_value' => $this->offer_value,
            'customer_head' => $this->customer_head,
            'customer_value' => $this->customer_value,
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
            'stripe_image' => $this->stripe_image,
            'customer_id' => \Barcode::generate(\Identifier::generate(Identifier::OFFER, auth()->user()->id, $this->offer_id)),
            'expiration_date' => $this->pass->expiration_date
        ];
    }
}
