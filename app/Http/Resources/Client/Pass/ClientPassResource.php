<?php

namespace App\Http\Resources\Client\Pass;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientPassResource extends JsonResource
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
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'pass_template' => new ClientPassTemplateResource($this->pass_template)
        ];
    }
}
