<?php

namespace App\Http\Resources\Admin\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientLogExportResource extends JsonResource
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
            'id' => $this->id,
            'message' => $this->message,
            'created_at' => $this->created_at->toDateTimeString()
        ];
    }
}
