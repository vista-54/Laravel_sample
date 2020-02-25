<?php

namespace App\Http\Resources\Admin\Pass;

use Illuminate\Http\Resources\Json\JsonResource;

class PassShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
//            + [
//                'pass_template' => $this->passTemplate,
//                'pass_locations' => $this->passLocations,
//                'currency' => 123,
//                'currency' => $this->user->loyaltyProgram->currency
//            ];
    }
}
