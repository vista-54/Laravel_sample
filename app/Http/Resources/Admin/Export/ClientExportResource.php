<?php

namespace App\Http\Resources\Admin\Export;

use App\Models\Invite;
use App\Models\Log;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientExportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $shop_ids = auth()->user()->shops()->pluck('id');
        if ($invite = Invite::where('email', $this->email)->wherein('shop_id', $shop_ids)->first()) {
            $shop = Shop::find($invite->shop_id) ? Shop::find($invite->shop_id)->name : 'No data';
        } else {
            $shop = 'No data';
        }
        return [
            'Joined date' => $this->created_at,
            'Joined Location' => $shop,
            'Unique ID' => $this->id,
            'IOS/Android' => $this->device_type,
            'Email' => $this->email,
            'First Name' => $this->first_name,
            'Last Name' => $this-> last_name,
            'Telephone' => $this->phone,
            'Country Code' => $this->country_code,
            'Birthday' => $this->birthday,
            'Timezone' => $this-> timezone,
            'Address' => $this->address,
            'Age' => Carbon::now()->diffInYears($this->birthday),
            'Race' => $this->race,
            'Points' => $this->clientLoyaltyProgram->point,
            'Total amount spent' => $this->clientShops->sum('amount'),
            'Total number of transactions' => $this->transactions()->count(),
            'Total number of offer used' => $this->offers()->wherePivot('used', 1)->count(),
            'Total number of coupons redeemed' => $this->passes()->count(),
            'Login Type (FB, Email)' => $this->social ? 'FB' : 'Email'
        ];
    }
}
