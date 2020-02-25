<?php

namespace App\Http\Resources\Admin\Export;

use App\Models\AreaManager;
use App\Models\Invite;
use App\Models\Log;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponExportResource extends JsonResource
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
            'Coupon ID' => $this->pass_id,
            'Client first name' => $this->client->first_name,
            'Client last name' => $this->client->last_name,
            'Coupon Value' => $this->pass->value,
            'currency' => $this->pass->user->loyaltyProgram->currency,
            'Redeemed amount' => $this->pass->value,
            'Redeemed location' => Shop::find($this->shop_id) ? Shop::find($this->shop_id)->name : 'No data',
            'Staff name' => $author
        ];
    }
}
