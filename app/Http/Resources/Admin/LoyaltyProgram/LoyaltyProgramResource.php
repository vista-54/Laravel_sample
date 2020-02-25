<?php

namespace App\Http\Resources\Admin\LoyaltyProgram;

use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyProgramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request) + ['card' => $this->card];
    }
}
