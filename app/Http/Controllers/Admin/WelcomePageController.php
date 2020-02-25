<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 10.07.2019
 * Time: 16:25
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\AreaManager;
use App\Models\Client;
use App\Models\ClientPass;
use App\Models\ClientShop;
use App\Models\Invite;
use App\Models\Offer;
use App\Models\Pass;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * @group Admin\Welcome-page actions
 */
class WelcomePageController extends ApiController
{
    /**
     * Return data for welcome-page
     *
     * @queryParam from Start time filter
     * @queryParam to End time filter
     *
     * @return JsonResponse
     */
    public function welcomePage(Request $request)
    {
        return $this->respond([
            'entity' => [
                'app' => $this->app($request),
                'card' => $this->card($request),
                'coupon' => $this->coupon($request),
                'shop_performance' => $this->performance($request),
                'top_clients' => $this->topClients($request),
                'currency' => auth()->user()->loyaltyProgram->currency
            ]
        ]);
    }

    /**
     * @return array
     */
    protected function app(Request $request)
    {
        $invite = Invite::when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
            $q->where('created_at', '>=', Carbon::parse($request->input('from')));
        })
            ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
            })
            ->whereHas('areaManager', function ($q) {
                /** @var AreaManager $q */
                $q->whereHas('user', function ($q) {
                    /** @var User $q */
                    $q->where('id', auth()->user()->id);
                });
            });
        $data = clone $invite;
        $revenue = auth()->user()->shops->sum(function ($item) use ($request) {
            $revenue = $item->clientShops()
                ->when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                    $q->where('created_at', '>=', Carbon::parse($request->input('from')));
                })
                ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                    $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                })->sum('amount');
            return $revenue;
        });
        return [
            'download_count' => auth()->user()->clients()->when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                $q->where('created_at', '>=', Carbon::parse($request->input('from')));
            })
                ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                    $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                })->count(),
            'invite_send' => $invite->count(),
            'invite_download' => $data->where('confirmed', 1)->count(),
            'revenues' => number_format($revenue, 2, '.', ',') . ' ' . auth()->user()->loyaltyProgram->currency
        ];
    }

    /**
     * @return array
     */
    protected function card(Request $request)
    {
        $invite = Invite::when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
            $q->where('created_at', '>=', Carbon::parse($request->input('from')));
        })
            ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
            })
            ->whereHas('areaManager', function ($q) {
                /** @var AreaManager $q */
                $q->whereHas('user', function ($q) {
                    /** @var User $q */
                    $q->where('id', auth()->user()->id);
                });
            });
        return [
            'points_cumulated' => ClientShop::when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                $q->where('created_at', '>=', Carbon::parse($request->input('from')));
            })
                ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                    $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                })->whereHas('client', function ($q) {
                    /** @var Client $q */
                    $q->where('user_id', auth()->id());
                })->get()->sum('point'),
            'redemption' => ClientPass::when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                $q->where('created_at', '>=', Carbon::parse($request->input('from')));
            })
                ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                    $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                })->count(),
            'transactions' => ClientShop::when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                $q->where('created_at', '>=', Carbon::parse($request->input('from')));
            })
                ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                    $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                })->whereHas('client', function ($q) {
                    /** @var Client $q */
                    $q->where('user_id', auth()->id());
                })->count(),
            'invitations' => $invite->count(),
            'offers' => auth()->user()->loyaltyProgram->offers()
                ->when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                    $q->where('created_at', '>=', Carbon::parse($request->input('from')));
                })
                ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                    $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                })
                ->limit(7)
                ->get(),
            'top_offers' => auth()->user()->loyaltyProgram->offers()->whereHas('clientOffers', function ($item) use ($request) {
                $item->when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                    $q->where('created_at', '>=', Carbon::parse($request->input('from')));
                })
                    ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                        $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                    });
            })
                ->withCount(['clientOffers' => function (Builder $query) use ($request) {
                    $query->when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                        $q->where('created_at', '>=', Carbon::parse($request->input('from')));
                    })
                        ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                            $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                        });
                }])->get()->sortBy(function ($item) {
                    return $item->client_offers_count;
                }, SORT_REGULAR, true)->values()
                ->slice(0, 6)->all()
        ];
    }

    /**
     * @return array
     */
    protected function coupon(Request $request)
    {
        return [
            'redemption' => ClientPass::when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                $q->where('created_at', '>=', Carbon::parse($request->input('from')));
            })
                ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                    $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                })->whereHas('client', function ($q) {
                    /** @var Client $q */
                    $q->where('user_id', auth()->id());
                })->count(),
            'top_coupons' => Pass::whereHas('clientPasses', function ($item) use ($request) {
                $item->when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                    $q->where('created_at', '>=', Carbon::parse($request->input('from')));
                })
                    ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                        $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                    });
            })
                ->where('user_id', auth()->id())->withCount(['clientPasses' => function (Builder $query) use ($request) {
                    $query->when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                        $q->where('created_at', '>=', Carbon::parse($request->input('from')));
                    })
                        ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                            $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                        });
                }])
                ->get()->sortBy(function ($item) {
                    return $item->client_passes_count;
                }, SORT_REGULAR, true)->values()
                ->slice(0, 6)->all()
        ];
    }

    /**
     * @return mixed
     */
    protected function performance(Request $request)
    {
        return auth()->user()->shops->map(function ($item) use ($request) {
            /** @var Shop $item */
            $item['currency'] = auth()->user()->loyaltyProgram->currency;
            $item['revenue'] = $item->clientShops()->when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                $q->where('created_at', '>=', Carbon::parse($request->input('from')));
            })
                ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                    $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                })->sum('amount');
            return $item;
        })->sortBy('revenue', SORT_REGULAR, true)->values();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function topClients(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        return $user->clients()
            ->when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                /** @var Client $q */
                $q->whereHas('clientShops', function ($q) use ($request) {
                    $q->where('client_shops.created_at', '>=', Carbon::parse($request->input('from')));
                });
            })
            ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                /** @var Client $q */
                $q->whereHas('clientShops', function ($q) use ($request) {
                    $q->where('client_shops.created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                });
            })
            ->with('clientShops')->get()
            ->map(function ($model) use ($request) {
                /** @var Client $model */
                $model['client_shops_amount'] = $model->clientShops()
                    ->when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                        $q->where('created_at', '>=', Carbon::parse($request->input('from')));
                    })
                    ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                        $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                    })
                    ->sum('amount');
                $model['client_shops_points'] = $model->clientShops()
                    ->when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                        $q->where('created_at', '>=', Carbon::parse($request->input('from')));
                    })
                    ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                        $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                    })->sum('point');
                return $model;
            })
            ->sortBy('client_shops_amount', SORT_REGULAR, true)
            ->values()
            ->slice(0, 6)->all();
    }

    public function chart(Request $request)
    {
        $shops = auth()->user()->shops;
        return $this->respond([
            'total_sales' => ClientShop::whereHas('client', function ($q) {
                /** @var Client $q */
                $q->whereHas('user', function ($q) {
                    /** @var User $q */
                    $q->where('id', auth()->id());
                });
            })->sum('amount'),
            'average_spend' => auth()->user()->clients->map(function ($model) {
                /** @var Client $model */
                return $model->clientShops->sum('amount');
            })->average(),
            'top_5_locations' => $this->performance($request),
            'total_sales_per_location' => $shops->map(function ($item) {
                /** @var Shop $item */
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'total_sales' => $item->clientShops()->count()
                ];
            }),
            'average_spend_per_location' => $shops->map(function ($item) {
                /** @var Shop $item */
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'average_spend' => $item->clientShops->average('amount')
                ];
            }),
            'top_promotions' => [],
            'top_5_items' => [],
            'top_5_items_by_sales_volume' => [],
            'active_customers' => [],
            'customer_lifetime_value' => [],
            'orders_by_age_group' => [],
            'active_customers_per_shop' => [],
            'new_vs_recurring_customers' => [],
            'return_rate' => [],
            'return_rate_of_first_order' => []
        ]);
    }


    /**
     * @return mixed
     */
    protected function shopStatistics(Request $request)
    {
        return auth()->user()->shops->map(function ($item) use ($request) {
            /** @var Shop $item */
            $item['currency'] = auth()->user()->loyaltyProgram->currency;
            $item['revenue'] = $item->clientShops()->when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
                $q->where('created_at', '>=', Carbon::parse($request->input('from')));
            })
                ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                    $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                })->sum('amount');
            return $item;
        })->sortBy('revenue', SORT_REGULAR, true)->values();
    }
}
