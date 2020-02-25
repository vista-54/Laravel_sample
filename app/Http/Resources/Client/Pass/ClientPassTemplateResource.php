<?php

namespace App\Http\Resources\Client\Pass;

use App\Services\Identifier;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientPassTemplateResource extends JsonResource
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
            'back_side_head' => $this->back_side_head,
            'back_side_value' => $this->back_side_value,
            'icon' => $this->icon,
            'background_image' => $this->background_image,
            'stripe_image' => $this->stripe_image,
            'customer_id' => \Barcode::generate(\Identifier::generate(Identifier::COUPON, auth()->user()->id, $this->id)),
            'unique_id' => \Identifier::generate(Identifier::COUPON, auth()->user()->id, $this->id),
            'unlimited' => $this->unlimited,
            'redeemed' => auth()->user()->passes()->where('id', $this->pass_id)->exists(),
            'expiration_date' => $this->pass->expiration_date,
            'margin_value' => $this->pass->margin_value
        ];
    }
}
