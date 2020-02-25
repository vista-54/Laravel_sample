<?php

namespace App\Http\Resources\Admin\Export;

use App\Models\AreaManager;
use App\Models\Log;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionExportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        if ($this->created_by && $author = AreaManager::find($this->created_by)) {
            $author = $author->name;
        } else {
            $author = null;
        }
        return [
            'Date Time' => $this->created_at,
            'Loyalty card ID',
            'Client first name' => $this->client->first_name,
            'Client last name' => $this->client->last_name,
            'Transaction Value' => $this->amount,
            'currency' => auth()->user()->loyaltyProgram->currency,
            'point added' => $this->point,
//            'post debited' => ,
            'value per point' => $this->client->user->loyaltyProgram->currency_value,
            'Location' => Shop::find($this->shop_id) ? Shop::find($this->shop_id)->name : 'No data',
            'Staff name' => $this->area_manager_id
        ];
    }
}
