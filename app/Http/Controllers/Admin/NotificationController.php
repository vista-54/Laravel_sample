<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Client\CampaignNotifyRequest;
use App\Http\Requests\Admin\Client\ClientNotifyRequest;
use App\Models\Client;
use App\Models\ClientShop;
use App\Models\Invite;
use Carbon\Carbon;
use Notify;

/**
 * @group Admin\notification actions
 */
class NotificationController extends ApiController
{
    const TWO_VISITS = 0;
    const WEEKEND_PURCHASE = 1;
    const NO_TRANSACTIONS_6 = 2;

    /**
     * Send notification to clients
     *
     * @bodyParam text string required
     * @bodyParam shop_id integer
     * @bodyParam race string
     * @bodyParam birthday integer
     * @bodyParam age integer
     * @bodyParam type integer
     * @bodyParam lifetime_value integer
     *
     * @param ClientNotifyRequest $request
     * @return \App\Models\Device[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|mixed
     */
    public function notifyAll(ClientNotifyRequest $request)
    {
        $clients_count = auth()->user()->clients()
            ->when($request->input('shop_id'), function ($q) use ($request) {
                /** @var Client $q */
                $q->whereHas('invites', function ($q) use ($request) {
                    /** @var Invite $q */
                    $q->where('shop_id', $request->input('shop_id'));
                });
            })
            ->when($request->input('race'), function ($q) use ($request) {
                /** @var Client $q */
                $q->where('race', $request->input('race'));
            })
            ->when($request->input('age'), function ($q) use ($request) {
                /** @var Client $q */
                $q->whereBetween('birthday', [Carbon::now()->subYears($request->input('age') + 1), Carbon::now()->subYears($request->input('age'))]);
            })
            ->when($request->input('birthday'), function ($q) use ($request) {
                /** @var Client $q */
                $q->whereMonth('birthday', $request->input('birthday'));
            })
            ->when($request->input('type'), function ($q) use ($request) {
                /** @var Client $q */
                switch ($request->input('type')) {
                    case self::TWO_VISITS:
                        $q->has('clientShops', '=', '2', 'and', function ($q) {
                            /** @var ClientShop $q */
                            $q->where('created_at', '>', Carbon::now()->subWeek()->toDateString());
                        });
                        break;
                    case self::WEEKEND_PURCHASE:
                        $q->whereHas('clientShops', function ($q) use ($request) {
                            /** @var ClientShop $q */
                            $q->whereRaw("weekday(created_at) in (5, 6)");
                        });
                        break;
                    case self::NO_TRANSACTIONS_6:
                        $q->whereHas('clientShops', function ($q) {
                            /** @var ClientShop $q */
                            $q->where('created_at', '<', Carbon::now()->subMonths(6)->toDateTimeString());
                        }, '=', 0);
                        break;
                }
            })
            ->get()
            ->map(function ($item) use ($request) {
                /** @var Client $item */
                $item->devices->map(function ($item) use ($request) {
                    Notify::sendNotification($item->token, 'NextCard', $request->input('text'));
                });
            })
            ->count();

        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.notification')
        ]);
    }


    public function notifyCampaign(CampaignNotifyRequest $request)
    {
        $clients_count = auth()->user()->clients()
            ->when($request->input('shop_id'), function ($q) use ($request) {
                /** @var Client $q */
                $q->whereHas('invites', function ($q) use ($request) {
                    /** @var Invite $q */
                    $q->where('shop_id', $request->input('shop_id'));
                });
            })
            ->when($request->input('race') && $request->input('race') !== 'All', function ($q) use ($request) {
                /** @var Client $q */
                $q->where('race', $request->input('race'));
            })
            ->when($request->input('age'), function ($q) use ($request) {
                /** @var Client $q */
                if (!empty($request->age)) {
                    $date_arr = collect($request->age)->map(function ($item) use ($q, $request) {
                        return explode('-', $item);
                    })->collapse()->sort()->values()->all();
                    $q->whereBetween('birthday', [Carbon::now()->subYears(end($date_arr)), Carbon::now()->subYears($date_arr[0])]);
                }
            })
            ->when($request->input('birthday'), function ($q) use ($request) {
                /** @var Client $q */
                $q->whereMonth('birthday', $request->input('birthday'));
            })
//            ->when($request->input('customer_type'), function ($q) use ($request) {
//                /** @var Client $q */
//                switch ($request->input('customer_type')) {
//                    case self::TWO_VISITS:
//                        $q->has('clientShops', '=', '2', 'and', function ($q) {
//                            /** @var ClientShop $q */
//                            $q->where('created_at', '>', Carbon::now()->subWeek()->toDateString());
//                        });
//                        break;
//                    case self::WEEKEND_PURCHASE:
//                        $q->whereHas('clientShops', function ($q) use ($request) {
//                            /** @var ClientShop $q */
//                            $q->whereRaw("weekday(created_at) in (5, 6)");
//                        });
//                        break;
//                    case self::NO_TRANSACTIONS_6:
//                        $q->whereHas('clientShops', function ($q) {
//                            /** @var ClientShop $q */
//                            $q->where('created_at', '<', Carbon::now()->subMonths(6)->toDateTimeString());
//                        }, '=', 0);
//                        break;
//                }
//            })
            ->get()
            ->map(function ($item) use ($request) {
                /** @var Client $item */
                $item->devices->map(function ($item) use ($request) {
//                    Notify::sendNotification($item->token, 'NextCard', $request->input('text'));
                });
            })
            ->count();
        $campaign = auth()->user()->campaigns()->create($request->all() + [
                'distribution' => $clients_count
            ]);
        if ($request->input('shop_id')) {
            $campaign->shops()->sync($request->input('shop_id'));
        }
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.notification')
        ]);
    }
}
