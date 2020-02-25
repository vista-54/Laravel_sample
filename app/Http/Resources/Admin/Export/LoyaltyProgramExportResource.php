<?php

namespace App\Http\Resources\Admin\Export;

use App\Models\AreaManager;
use App\Models\Log;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyProgramExportResource extends JsonResource
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
            'Coupon ID' => $this->offer_id,
            'Client first name' => $this->client->first_name,
            'Client last name' => $this->client->last_name,
            'Offer Value points' => $this->offer->points_cost,
            'points per offer' => $this->offer->points_cost,
            'currency' => $this->client->user->loyaltyCard->currency,
            'Redeemed amount' => $this->offer->points_cost,
            'Redeemed location' => Shop::find($this->shop_id) ? Shop::find($this->shop_id)->name : 'No data',
            'Staff name' => $author
        ];
    }
}
