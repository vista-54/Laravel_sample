<?php

namespace App\Http\Resources\Admin\Transaction;

use App\Models\AreaManager;
use App\Services\Identifier;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionListResource extends JsonResource
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
                'name' => $this->client->full_name,
                'area_manager' => $this->area_manager_id ? AreaManager::find($this->area_manager_id)->name : 'No data',
                'client_loyalty_id' => \Identifier::generate(Identifier::LOUALTY, $this->client->id, $this->client->user->loyaltyProgram->id),
                'total_points' => $this->client->clientLoyaltyProgram->point
            ];
    }
}
