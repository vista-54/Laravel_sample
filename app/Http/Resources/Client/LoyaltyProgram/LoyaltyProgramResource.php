<?php

namespace App\Http\Resources\Client\LoyaltyProgram;

use App\Http\Resources\Client\Card\CardResource;
use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyProgramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request) + [
                'card' => new CardResource($this->card),
                'user' => $this->user,
                'terms' => $this->contactsTerm
            ];
    }
}
