<?php

namespace App\Http\Resources\Admin\Campaign;

use App\Models\Campaign;
use App\Models\ClientOffer;
use App\Models\ClientPass;
use App\Models\Pass;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $redemption = ClientOffer::where('used', 1)
            ->where('created_at', '>=', $this->campaign_start)
            ->where('created_at', '<=', $this->campaign_end)
            ->count();

            $revenues = ClientPass::where('created_at', '>=', $this->campaign_start)
                ->where('created_at', '<=', $this->campaign_end)
                ->get()->sum(function ($item) {
                    /** @var ClientOffer $item */
                    return $item->pass->value;
                });

            if ($this->shops()->pluck('name')->toArray()) {
                $shops = implode(', ', $this->shops()->pluck('name')->toArray());
            } else {
                $shops = 'All';
            }
//        dd((int)$this->distribution, (int)$redemption);
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'campaign_name' => $this->campaign_name,
            'shop' => $shops,
            'shop_list' => $this->shops,
//            'shop' => $this->shop_id == null ? 'All' : $this->shop->name,
            'shop_id' => $this->shop_id,
            'race' => $this->race ?? 'All',
            'age' => $this->age,
            'birthday' => $this->month ?? 'All',
            'customer_type' => $this->customer_type,
            'type' => $this->type,
            'distribution' => $this->distribution,
            'campaign_start' => Carbon::parse($this->campaign_start)->toDateString(),
            'campaign_end' => Carbon::parse($this->campaign_end)->toDateString(),
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            'redemption' => $redemption,
            'conversion' => (int)$this->distribution == 0 || (int)$redemption == 0
                ? 0 . '%'
                : number_format(round((int)$redemption / (int)$this->distribution, 2), 2, '.', ',') * 100 . '%',
            'revenues' => $revenues . ' ' . auth()->user()->loyaltyProgram->currency,
            'tag' => $this->tag,
            'text' => $this->text,
            'date_time' => $this->date_time,
            'frequency' => $this->frequency ?? 'All',
            'region' => $this->region,
            'trans_total_value' => $this->trans_total_value,
            'media' => $this->media,
            'client_group_id' => $this->client_group_id,
            'client_group_name' => $this->clientGroup()->exists() ? $this->clientGroup->name : null,
            'margin_value' => $this->margin_value,
        ];
    }
}
