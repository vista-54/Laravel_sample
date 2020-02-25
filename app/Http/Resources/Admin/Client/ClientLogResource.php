<?php

namespace App\Http\Resources\Admin\Client;

use App\Models\AreaManager;
use App\Models\Client;
use App\Models\Shop;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        if ($this->shop_id !== null && Shop::find($this->shop_id)) {
            $shop = Shop::find($this->shop_id)->name;
        } else {
            $shop = null;
        }
        if ($this->area_manager_id !== null && AreaManager::find($this->area_manager_id)) {
            $area_manager_id = AreaManager::find($this->area_manager_id)->name;
        } elseif ($this->area_manager_id === 0) {
            $area_manager_id = Client::find($this->logable_id)->user->full_name;
        } else {
            $area_manager_id = null;
        }
        return parent::toArray($request) + [
                'shop_name' => $shop,
                'manager_name' => $area_manager_id
            ];
    }
}
