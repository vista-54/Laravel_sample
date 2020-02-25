<?php

namespace App\Http\Resources\Admin\Campaign;

use App\Models\Client;
use App\Models\ClientGroup;
use App\Models\ClientOffer;
use App\Models\ClientPass;
use App\Models\ClientShop;
use App\Models\Invite;
use App\Models\Shop;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class AnalyticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'campaign_name' => $this->campaign_name,
            'total_sales' => $this->totalSales(),
            'sales_distribution_per_store' => $this->salesDistributionPerStore(),
            'total_success_Rate' => $this->totalSuccessRate(),
            'received' => $this->received(),
            'transformed' => $this->transformed(),
            'store_type' => $this->storeType(),
            'customer_group' => $this->customerType(),
            'sales_contribution' => $this->salesContribution(),
            'sales_value_promoted_vs_category' => $this->salesValueVsCategory()
        ];
    }

    /**
     * Total sales for each campaign
     *
     * @return mixed
     */
    private function totalSales()
    {
        return ClientShop::whereHas('client', function ($client) {
            /** @var Client $client */
            if ($this->client_group_id) {
                $client->whereHas('clientGroups', function ($groups) {
                    /** @var ClientGroup $groups */
                    $groups->where('id', $this->id);
                });
            }
            $client->where('user_id', auth()->id());
        })
            ->where('created_at', '>=', $this->campaign_start)
            ->where('created_at', '<=', $this->campaign_end)
            ->sum('amount');
    }

    /**
     * Campaign sales distribution per store
     *
     * @return array
     */
    private function salesDistributionPerStore()
    {
        if ($this->shop_id !== null) {
            $shop = Shop::find($this->shop_id);
            return [
                'id' => $shop->id,
                'name' => $shop->name,
                'sum' => ClientShop::whereHas('client', function ($client) {
                    /** @var Client $client */
                    if ($this->client_group_id) {
                        $client->whereHas('clientGroups', function ($groups) {
                            /** @var ClientGroup $groups */
                            $groups->where('id', $this->id);
                        });
                    }
                    $client->where('user_id', auth()->id());
                })
                    ->where('shop_id', $this->shop_id)
                    ->where('created_at', '>=', $this->campaign_start)
                    ->where('created_at', '<=', $this->campaign_end)
                    ->sum('amount')
            ];
        } else {
            return auth()->user()->shops->map(function ($shop) {
                /** @var Shop $shop */
                $sum = $shop->clientShops()->where('shop_id', $this->shop_id)
                    ->where('created_at', '>=', $this->campaign_start)
                    ->where('created_at', '<=', $this->campaign_end)
                    ->sum('amount');
                return [
                    'id' => $shop->id,
                    'name' => $shop->name,
                    'sum' => $sum
                ];
            });
        }
    }

    /**
     * number of coupons redeemed vs total customers in group
     *
     * @return array
     */
    private function totalSuccessRate()
    {
        $redeemed = ClientPass::whereHas('client', function ($client) {
            /** @var Client $client */
            if ($this->client_group_id) {
                $client->whereHas('clientGroups', function ($groups) {
                    /** @var ClientGroup $groups */
                    $groups->where('id', $this->id);
                });
            }
            $client->where('user_id', auth()->id());
        })->count();
        if ($this->client_group_id) {
            $group = auth()->user()->clients()->whereHas('clientGroups', function ($groups) {
                /** @var ClientGroup $groups */
                $groups->where('id', $this->id);
            })->count();
        } else {
            $group = auth()->user()->clients()->count();
        }
        return [
            'redeemed_coupons_count' => $redeemed,
            'group_customers_count' => $group,
            'group' => $this->client_group_id ? true : false,
        ];
    }

    /**
     * how many peopled received the campaign
     *
     * @return mixed
     */
    private function received()
    {
        return $this->user->clients()
            ->where(function ($q) {
                /** @var Client $q */
                // birthday filter
                if ($this->month !== null) {
                    $q->whereMonth('birthday', $this->month);
                }
                //age filter
                if ($this->age !== null) {
                    $dates = explode('-', $this->age);
                    $q->whereBetween('birthday', [
                        Carbon::now()->subYears($dates[1]),
                        Carbon::now()->subYears($dates[0])
                    ]);

                }
                // Venue filter
                if ($this->shop_id !== null) {
                    $q->whereHas('invites', function ($q) {
                        /** @var Invite $q */
                        $q->where('shop_id', $this->shop_id);
                    });
                }
                // race filter
                if ($this->race !== 'All') {
                    $q->where('race', $this->race);
                }
            })->count();
    }

    /**
     * number of coupons redeemed of selected CG
     *
     * @return string
     */
    private function transformed()
    {
        if ($this->client_group_id) {
            $redeemed = ClientPass::whereHas('client', function ($client) {
                /** @var Client $client */
                if ($this->client_group_id) {
                    $client->whereHas('clientGroups', function ($groups) {
                        /** @var ClientGroup $groups */
                        $groups->where('id', $this->id);
                    });
                }
                $client->where('user_id', auth()->id());
            })->count();
        } else {
//            $redeemed = 'No group selected';
            $redeemed = 0;
        }

        return $redeemed;
    }

    /**
     * popUp store, supermarket.(will be updated soon)
     *
     * @return Shop|Shop[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    private function storeType()
    {
        if ($this->shop_id !== null) {
            return Shop::find($this->shop_id);
        } else {
            return auth()->user()->shops;
        }
    }

    /**
     * Customer group name
     *
     * @return ClientGroup|ClientGroup[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    private function customerType()
    {
        return $this->client_group_id !== null ? ClientGroup::find($this->client_group_id) : null;
    }

    private function salesContribution()
    {
        $sells = ClientShop::whereHas('client', function ($client) {
            /** @var Client $client */
            if ($this->client_group_id) {
                $client->whereHas('clientGroups', function ($groups) {
                    /** @var ClientGroup $groups */
                    $groups->where('id', $this->id);
                });
            }
            $client->where('user_id', auth()->id());
        })
            ->where('created_at', '>=', $this->campaign_start)
            ->where('created_at', '<=', $this->campaign_end)
            ->count();

        $offers = ClientOffer::whereHas('client', function ($client) {
            /** @var Client $client */
            if ($this->client_group_id) {
                $client->whereHas('clientGroups', function ($groups) {
                    /** @var ClientGroup $groups */
                    $groups->where('id', $this->id);
                });
            }
            $client->where('user_id', auth()->id());
        })
            ->where('created_at', '>=', $this->campaign_start)
            ->where('created_at', '<=', $this->campaign_end)
            ->count();

        $passes = ClientPass::whereHas('client', function ($client) {
            /** @var Client $client */
            if ($this->client_group_id) {
                $client->whereHas('clientGroups', function ($groups) {
                    /** @var ClientGroup $groups */
                    $groups->where('id', $this->id);
                });
            }
            $client->where('user_id', auth()->id());
        })
            ->where('created_at', '>=', $this->campaign_start)
            ->where('created_at', '<=', $this->campaign_end)
            ->count();
        return [
            // Out of 1,000 tickets, 120 tickets used the promotion 20% off
            'transaction_value' => [
                'sells' => $sells,
                'offers' => $offers
            ],
            // Transaction value share total sales vs total sales from these customers who used the coupons
            'transaction_contribution' => [
                'sells' => $sells,
                'passes' => $passes
            ]
        ];
    }

    /**
     * Sales value of items that are promoted VS category
     *
     *
     */
    private function salesValueVsCategory()
    {
//        return 'In Progress';
        return 0;
    }
}
