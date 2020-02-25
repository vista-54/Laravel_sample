<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 02.07.2019
 * Time: 13:34
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\AreaManager;
use App\Models\ClientOffer;
use App\Models\Invite;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
/**
 * @group Admin\performance actions
 */
class PerformanceController extends ApiController
{

    /**
     * Display shop performance.
     *
     * @queryParam from Start time filter
     * @queryParam to End time filter
     * @queryParam manager_id Filter by manager id
     * @queryParam shop_id Filter by shop id
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function shop(Request $request)
    {
         $data = auth()->user()
            ->shops()
            ->when($request->has('shop_id'), function ($q) use ($request) {
                /** @var Shop $q */
                $q->where('id', $request->input('shop_id'));
            })
            ->when($request->has('manager_id'), function ($q) use ($request) {
                /** @var Shop $q */
                $q->whereHas('areaManagers', function ($q) use ($request) {
                    $q->where('id', $request->input('manager_id'));
                });
            })
            ->get()
            ->map(function ($item) use ($request) {
                /** @var Shop $item */
                $item['amount'] = $item->clientShops()
                    ->when($request->has('to'), function ($q) use ($request) {
                        $q->where('created_at', '<', Carbon::parse($request->input('to')));
                    })
                    ->when($request->has('shop_id'), function ($q) use ($request) {
                        /** @var Shop $q */
                        $q->where('id', $request->input('shop_id'));
                    })
                    ->get()->sum('amount');
                $item['install'] = $item->invites()
                    ->when($request->has('to'), function ($q) use ($request) {
                        $q->where('created_at', '<', Carbon::parse($request->input('to')));
                    })
                    ->when($request->has('shop_id'), function ($q) use ($request) {
                        /** @var Shop $q */
                        $q->where('id', $request->input('shop_id'));
                    })
                    ->where('confirmed', 1)->when($request->has('manager_id'), function ($q) use ($request) {
                    /** @var Shop $q */
                    $q->where('area_manager_id', $request->input('manager_id'));
                })
                    ->count();
                $item['collect'] = $this->collect($item, $request);
                $item['redeem'] = $this->redeem($item, $request);
                $item['currency'] = auth()->user()->loyaltyProgram->currency;
                $item['number_of_shops'] = auth()->user()->shops()->count();
                $item['sold'] = $item->clientShops()->withCount('products')->get()->sum('products_count');
                $item['clients'] = $item->clientShops()->where('amount', '>', 0)->groupBy('client_id')->count();
                $item['invite'] = $item->invites()
                    ->when($request->has('to'), function ($q) use ($request) {
                        $q->where('created_at', '<', Carbon::parse($request->input('to')));
                    })
                    ->when($request->has('shop_id'), function ($q) use ($request) {
                        /** @var Shop $q */
                        $q->where('id', $request->input('shop_id'));
                    })
                    ->when($request->has('manager_id'), function ($q) use ($request) {
                        /** @var Shop $q */
                        $q->where('area_manager_id', $request->input('manager_id'));
                    })
                    ->count();
                return $item;
            });
         return  $this->respond([
             'entity' => $data,
             'number_of_shops' => auth()->user()->shops()->count()
         ]);
    }

    protected function redeem(Shop $shop, Request $request)
    {
        return ClientOffer::where('shop_id', $shop->id)
            ->where('used', 1)
            ->when($request->has('manager_id'), function ($q) use ($request) {
                /** @var Shop $q */
                $q->where('created_by', $request->input('manager_id'));
            })
            ->when($request->has('to'), function ($q) use ($request) {
                $q->where('created_at', '<', Carbon::parse($request->input('to')));
            })
            ->when($request->has('shop_id'), function ($q) use ($request) {
                /** @var Shop $q */
                $q->where('id', $request->input('shop_id'));
            })
            ->count();
    }

    protected function collect(Shop $shop, Request $request)
    {
        return $shop->clientShops()
            ->when($request->has('to'), function ($q) use ($request) {
                $q->where('created_at', '<', Carbon::parse($request->input('to')));
            })
            ->when($request->has('shop_id'), function ($q) use ($request) {
                /** @var Shop $q */
                $q->where('id', $request->input('shop_id'));
            })
            ->when($request->input('manager_id'), function ($q) use ($request) {
            /** @var Shop $q */
            $q->where('created_by', $request->input('manager_id'));
        })->get()->sum(function ($item) {
            $lp = $item->client->loyaltyProgram()->first();
            return $item->point;
//            return $lp->currency_value !== null && $lp->currency_value >= 1 ? intdiv($item->amount, $lp->currency_value) : 0;
        });
    }
}
