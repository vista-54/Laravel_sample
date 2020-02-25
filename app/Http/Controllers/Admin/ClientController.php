<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ClientExport;
use App\Exports\CouponExport;
use App\Exports\LoyaltyProgramExport;
use App\Exports\TransactionExport;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Client\ChangePointsRequest;
use App\Http\Requests\Admin\Client\ClientNotifyRequest;
use App\Http\Requests\Admin\Client\ClientStoreRequest;
use App\Http\Requests\Admin\Client\ClientUpdateRequest;
use App\Http\Requests\Admin\Client\SetOfferRequest;
use App\Http\Requests\Admin\Client\AddTransactionRequest;
use App\Http\Resources\Admin\Client\ClientListResource;
use App\Http\Resources\Admin\Client\ClientShowResource;
use App\Http\Resources\Admin\Client\MerchantClientResource;
use App\Http\Resources\Admin\Transaction\TransactionListResource;
use App\Http\Resources\Collection;
use App\Models\Client;
use App\Models\ClientLoyaltyProgram;
use App\Models\ClientShop;
use App\Models\Device;
use App\Models\Log;
use App\Models\Offer;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Identifier;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Notify;
use Excel;
use DB;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * @group Admin\client actions
 */
class ClientController extends ApiController
{
    /**
     * Display a listing of merchant clients
     *
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function merchantClients(User $user, Request $request)
    {
        $inactive = $user->clients()->whereDoesntHave('logs', function ($q) {
            /** @var Log $q */
            $q->where('created_at', '>', Carbon::now()->subMonth());
        })->count();
        if ($all = $user->clients()->count()) {
            $churn = 100 - round($inactive / $all * 100, 2);
            if ($inactive == 0) {
                $churn = 100;
            }
        } else {
            $churn = 0;
        }
        $data = $user
            ->clients()
            ->when($request->has('loyalty_id'), function ($q) use ($request) {
                /** @var Client $q */
                $q->whereHas('clientLoyaltyProgram', function ($q) use ($request) {
                    /** @var ClientLoyaltyProgram $q */
                    $q->where('client_loyalty_id', 'like', '%' . $request->input('loyalty_id') . '%');
                });
            })
            ->when($request->has('phone'), function ($q) use ($request) {
                /** @var Client $q */
                $q->where('phone', 'like', '%' . $request->input('phone') . '%');
            })
            ->when($request->has('first_name'), function ($q) use ($request) {
                /** @var Client $q */
                $q->where('first_name', 'like', '%' . $request->input('first_name') . '%');
            })
            ->when($request->has('last_name'), function ($q) use ($request) {
                /** @var Client $q */
                $q->where('last_name', 'like', '%' . $request->input('last_name') . '%');
            })
            ->join('client_loyalty_programs', 'client_loyalty_programs.client_id', '=', 'clients.id')
            ->leftJoin('client_shops', 'client_shops.client_id', '=', 'clients.id')
            ->select([
                'clients.id AS id',
                'user_id',
                'phone',
                'email',
                'first_name',
                'last_name',
                'address',
                'timezone',
                'code',
                'social',
                'block',
                'race',
                'lifetime_value',
                'birthday',
                'country_code',
                'device_type',
                DB::raw('SUM(client_loyalty_programs.point) AS points'),
                DB::raw('SUM(client_shops.amount) AS transaction_total'),
                DB::raw('MAX(client_shops.created_at) AS transaction'),
            ])
            ->groupBy('clients.id')
            ->when($request->has('points') && $request->input('priority') == 'points', function ($q) use ($request) {
                /** @var Client $q */
                $q->orderBy('points', $request->input('points', 'desc'));

            })
            ->when($request->has('transaction') && $request->input('priority') == 'transaction', function ($q) use ($request) {
                /** @var Client $q */
                $q->orderBy('transaction', $request->input('transaction', 'desc'));
            })
            ->when($request->has('frequency_purchase') && $request->input('priority') == 'frequency_purchase', function ($q) use ($request) {
                /** @var Client $q */
                $q->withCount('clientShops')
                    ->orderBy('client_shops_count', $request->input('frequency_purchase', 'desc'));
            })
            ->when($request->has('birthday') && $request->input('priority') == 'birthday', function ($q) use ($request) {
                /** @var Client $q */
                $q->orderBy('birthday', $request->input('birthday', 'desc'));
            })
            ->when($request->has('address') && $request->input('priority') == 'address', function ($q) use ($request) {
                /** @var Client $q */
                $q->orderBy('address', $request->input('address', 'desc'));
            })
            ->when($request->has('transaction_total') && $request->input('priority') == 'transaction_total', function ($q) use ($request) {
                /** @var Client $q */
                $q->orderBy('transaction_total', $request->input('transaction_total', 'desc'));
            })
            ->when($request->has('transaction_total_from'), function ($q) use ($request) {
                /** @var Client $q */
                $q->whereHas('clientShops', function ($q) use ($request) {
                    /** @var Client $q */
                    $q->where('created_at', '>=', $request->input('transaction_total_from'));
                });
            })
            ->when($request->has('transaction_total_to'), function ($q) use ($request) {
                /** @var Client $q */
                $q->whereHas('clientShops', function ($q) use ($request) {
                    /** @var Client $q */
                    $q->where('created_at', '<=', $request->input('transaction_total_to'));
                });
            })

//            ->count();
            ->paginate($request->input('limit'));
//        return $data;
        return $this->respondWithPagination2($data, new Collection(MerchantClientResource::collection($data)), $churn);
    }

    /**
     * Create new client.
     *
     * @bodyParam user_id integer required
     * @bodyParam phone string required
     * @bodyParam email string required
     * @bodyParam password string required
     * @bodyParam address string
     * @bodyParam first_name string
     * @bodyParam last_name string
     * @bodyParam birthday string
     * @bodyParam race string
     *
     * @param Client $client
     * @param ClientStoreRequest $request
     * @return Response
     * @throws \Throwable
     */
    public function store(Client $client, ClientStoreRequest $request)
    {
        DB::beginTransaction();
        $model = $client->create($request->data());
        $model->loyaltyProgram()->attach($model->user->loyaltyProgram->id, ['client_loyalty_id' => \Identifier::generate(Identifier::LOUALTY, $model->id, $model->user->loyaltyProgram->id)]);
        DB::commit();
        return $this->respondCreated(trans('admin/message.client_create'), $model);
    }

    /**
     * Display client data.
     *
     * @param Client $client
     * @return Response
     */
    public function show(Client $client)
    {
        return $this->respond([
            'entity' => new ClientShowResource($client)
        ]);
    }

    /**
     * Update client.
     *
     * @bodyParam user_id integer
     * @bodyParam phone string
     * @bodyParam email string
     * @bodyParam password string
     * @bodyParam address string
     * @bodyParam first_name string
     * @bodyParam last_name string
     * @bodyParam birthday string
     * @bodyParam race string
     *
     * @param Client $client
     * @param ClientUpdateRequest $request
     * @return Response
     */
    public function update(Client $client, ClientUpdateRequest $request)
    {
        $client->update($request->validated());
        return $this->respondCreated(trans('admin/message.client_update'), $client);
    }

    /**
     * Remove client.
     *
     * @param Client $client
     * @return Response
     * @throws \Exception
     */
    public function destroy(Client $client)
    {
        $client->delete();
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.client_delete')
        ]);
    }

    /**
     * Send notification to specific client
     * @param Client $client
     * @param ClientNotifyRequest $request
     * @return Device[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|mixed
     */
    public function notify(Client $client, ClientNotifyRequest $request)
    {
        return $client->devices->map(function ($item) use ($request) {
            Notify::sendNotification($item->token, 'NextCard', $request->input('text'));
        })->count();
    }

    /**
     * Export client data to csv file
     *
     * @return BinaryFileResponse
     */
    public function clientExport()
    {
        if (Excel::store(new ClientExport(), 'public/' . auth()->id() . '/' . 'clientData.csv')) {
            return $this->respond([
                'link' => url('storage/' . auth()->id() . '/' . 'clientData.csv')
            ]);
        } else {
            return $this->respond([
                'status' => false
            ]);
        }
    }

    /**
     * Export loyalty data to csv file
     *
     * @return BinaryFileResponse
     */
    public function loyaltyExport()
    {
        if (Excel::store(new LoyaltyProgramExport(), 'public/' . auth()->id() . '/' . 'loyaltyProgramData.csv')) {
            return $this->respond([
                'link' => url('storage/' . auth()->id() . '/' . 'loyaltyProgramData.csv')
            ]);
        } else {
            return $this->respond([
                'status' => false
            ]);
        }
    }

    /**
     * Export coupon data to csv file
     *
     * @return BinaryFileResponse
     */
    public function couponExport()
    {
        if (Excel::store(new CouponExport(), 'public/' . auth()->id() . '/' . 'couponData.csv')) {
            return $this->respond([
                'link' => url('storage/' . auth()->id() . '/' . 'couponData.csv')
            ]);
        } else {
            return $this->respond([
                'status' => false
            ]);
        }
    }

    /**
     * Export coupon data to csv file
     *
     * @return BinaryFileResponse
     */
    public function transactionExport()
    {
        if (Excel::store(new TransactionExport(), 'public/' . auth()->id() . '/' . 'transactionData.csv')) {
            return $this->respond([
                'link' => url('storage/' . auth()->id() . '/' . 'transactionData.csv')
            ]);
        } else {
            return $this->respond([
                'status' => false
            ]);
        }
    }

    /**
     * Change client points count
     *
     * @param Client $client
     * @param ChangePointsRequest $request
     * @return JsonResponse
     */
    public function changePoints(Client $client, ChangePointsRequest $request)
    {
        if ($client->clientLoyaltyProgram->point + $request->input('points') < 0) {
            $client->clientLoyaltyProgram()->update(['point' => 0]);
        } else {
            $client->clientLoyaltyProgram()->increment('point', $request->input('points'));
        }
        $client->logs()->create([
            'message' => 'Change balance for ' . $request->input('points') . ' points',
            'point' => $request->input('points'),
            'area_manager_id' => 0
        ]);
        $client->devices->map(function ($item) use ($request) {
            /** @var Device $item */
            Notify::sendNotification($item->token, 'NextCard', 'Merchant ' . auth()->user()->business . ' added you ' . $request->input('points') . ' points');
        });
        return $this->respondCreated(trans('admin/message.client_points'), [
            'points' => Client::find($client->id)->clientLoyaltyProgram->point
        ]);
    }

    /**
     * Reduce client points
     *
     * @param Client $client
     * @param ChangePointsRequest $request
     * @return JsonResponse
     */
    public function reducePoints(Client $client, ChangePointsRequest $request)
    {
        if ($client->clientLoyaltyProgram->point - $request->input('points') < 0) {
            $client->clientLoyaltyProgram()->update(['point' => 0]);
        } else {
            $client->clientLoyaltyProgram()->decrement('point', $request->input('points'));
        }
        $client->logs()->create([
            'message' => 'Reduce balance for ' . $request->input('points') . ' points',
            'point' => $request->input('points'),
            'area_manager_id' => 0
        ]);
        $client->devices->map(function ($item) use ($request) {
            /** @var Device $item */
            Notify::sendNotification($item->token, 'NextCard', 'Merchant ' . auth()->user()->business . ' reduce ' . $request->input('points') . ' points');
        });
        return $this->respondCreated(trans('admin/message.client_points'), [
            'points' => Client::find($client->id)->clientLoyaltyProgram->point
        ]);
    }

    /**
     * Block client
     *
     * @param Client $client
     * @return JsonResponse
     */
    public function block(Client $client)
    {
        if ($client->block === 1) {
            $client->block = 0;
            return $this->respondCreated(trans('admin/message.client_unlocked'), $client->save());
        } else {
            $client->block = 1;
            return $this->respondCreated(trans('admin/message.client_locked'), $client->save());
        }
    }

    /**
     * Set offer to client
     *
     * @bodyParam offer_id integer required
     *
     * @param Client $client
     * @param SetOfferRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function setOffer(Client $client, SetOfferRequest $request)
    {
        $offer = Offer::find($request->input('offer_id'));
        if ($client->offers()->wherePivot('used', 0)->exists()) {
            throw new \Exception('Client can have only one active offer.', '401');
        }
        DB::beginTransaction();
        $client->offers()->attach($offer->id, ['created_by' => $client->id]);
        Transaction::create([
            'client_id' => $client->id,
            'point' => $offer->points_cost,
            'shop_name' => 'Set offer by ' . auth()->user()->full_name,
            'status' => 0
        ]);
        if ($client->clientLoyaltyProgram->point >= $offer->points_cost) {
            $client->devices->map(function ($item) use ($offer) {
                /** @var Device $item */
                Notify::sendNotification($item->token, 'NextCard', 'Your offer ' . $offer->name . ' is ready', ['entity' => $offer, 'actions' => 'offer_update']);
            });

            $client->increment('lifetime_value', $offer->points_cost);
            DB::commit();
            $client->logs()->create([
                'message' => 'Use offer ' . $offer->name,
                'point' => '-' . $offer->points_cost,
                'area_manager_id' => 0
            ]);

            return $this->respond([
                'entity' => $client->clientLoyaltyProgram()->update(['point' => $client->clientLoyaltyProgram->point - $offer->points_cost]),
                'message' => trans('client/message.loyalty_buy_success')
            ]);

        } else {
            DB::rollBack();
            throw new \Exception('Client have not enough points to use this offer', '401');
        }
    }


    /**
     * Get clients statistic on clients page
     *
     * @queryParam from Start time filter
     * @queryParam to End time filter
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function boxes(Request $request)
    {
        /** @var User $currency */
        $currency = auth()->user()->loyaltyProgram->currency;

        $data = auth()->user()->clients()
            ->when($request->has('from'), function ($q) use ($request) {
                $q->whereHas('clientShops', function ($q) use ($request) {
                    $q->where('client_shops.created_at', '>=', Carbon::parse($request->input('from')));
                });
            })
            ->when($request->has('to'), function ($q) use ($request) {
                $q->whereHas('clientShops', function ($q) use ($request) {
                    $q->where('client_shops.created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                });
            })
            ->get()
            ->map(function ($item) use ($request) {
                /** @var Client $item */
                $item['amounts'] = $item->clientShops()->sum('amount');
                return $item;
            })
            ->sortByDesc('amounts')
            ->values()
            ->slice(0, 6)->all();

        $users = auth()->user()->clients()
            ->whereHas('clientShops', function ($q) use ($request) {
                $q->when($request->has('from'), function ($q) use ($request) {
                    $q->where('created_at', '>=', Carbon::parse($request->input('from')));
                })
                    ->when($request->has('to'), function ($q) use ($request) {
                        $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                    });
            })
            ->count();

        $amount = ClientShop::whereIn('client_id', auth()->user()->clients()->pluck('id'))
            ->when($request->has('from'), function ($q) use ($request) {
                $q->where('created_at', '>=', Carbon::parse($request->input('from')));
            })
            ->when($request->has('to'), function ($q) use ($request) {
                $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
            })->sum('amount');

        return $this->respond([
            'top_spenders' => $data,
            'top_repeaters' => auth()->user()->clients()
                ->withCount(['clientShops' => function ($q) use ($request) {
                    $q->when($request->has('from'), function ($q) use ($request) {
                        $q->where('created_at', '>=', Carbon::parse($request->input('from')));
                    })
                        ->when($request->has('to'), function ($q) use ($request) {
                            $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                        })->where('id', '<>', null);
                }])
                ->whereHas('clientShops', function ($q) use ($request) {
                    $q->when($request->has('from'), function ($q) use ($request) {
                        $q->where('created_at', '>=', Carbon::parse($request->input('from')));
                    })
                        ->when($request->has('to'), function ($q) use ($request) {
                            $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                        })->where('id', '<>', null);
                })
                ->orderByDesc('client_shops_count')
                ->limit(6)
                ->get(),
            'customer_lifestyle_value' => $users ? number_format(round((int)$amount / (int)$users, 2), 2, '.', ',') . ' ' . $currency : 0 . ' ' . $currency
        ]);

    }

    /**
     * @param Client $client
     * @param Request $request
     * @return mixed
     */
    public function clientTransaction(Client $client, Request $request)
    {
        $transactionList = $client->transactions()->paginate($request->input('limit', 20));
        return $this->respondWithPagination($transactionList, new Collection(TransactionListResource::collection($transactionList)));
    }

    public function clientList(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();
        $data = $user->clients()
            ->when($request->has('region'), function ($q) use ($request) {

                $q->where('address', 'like', '%' . $request->input('region') . '%');
            })
            ->when($request->has('frequency'), function ($q) use ($request) {
                switch ($request->input('type')) {
                    case NotificationController::TWO_VISITS:
                        $q->has('clientShops', '=', '2', 'and', function ($q) {
                            /** @var ClientShop $q */
                            $q->where('created_at', '>', Carbon::now()->subWeek()->toDateString());
                        });
                        break;
                    case NotificationController::WEEKEND_PURCHASE:
                        $q->whereHas('clientShops', function ($q) use ($request) {
                            /** @var ClientShop $q */
                            $q->whereRaw("weekday(created_at) in (5, 6)");
                        });
                        break;
                    case NotificationController::NO_TRANSACTIONS_6:
                        $q->whereHas('clientShops', function ($q) {
                            /** @var ClientShop $q */
                            $q->where('created_at', '<', Carbon::now()->subMonths(6)->toDateTimeString());
                        }, '=', 0);
                        break;
                }
            })
            ->when($request->has('age'), function ($q) use ($request) {
                $range = explode('-', $request->input('age'));
                $q->whereBetween('birthday', [Carbon::now()->subYears($range[1]), Carbon::now()->subYears($range[0])]);
            })
            ->paginate($request->input('limit', 15));
        return $this->respondWithPagination($data, new Collection(ClientListResource::collection($data)));
    }

    /**
     * @param AddTransactionRequest $request
     * @param Client $client
     * @return JsonResponse
     * @throws \Exception
     */
    public function storeTransaction(AddTransactionRequest $request, Client $client)
    {
        DB::beginTransaction();
        $lp = $client->loyaltyProgram()->first();
        $area_manager = Shop::find($request->input('shop_id'))->areaManagers()->first();
        /** @var ClientShop $clientShop */
        $client->clientShops()->create($request->validated() + [
                'point' => DB::raw('point+' . intdiv($request->input('amount'), $lp->currency_value)),
                'type' => ClientShop::TYPE_LOYALTY
            ]);

        Transaction::create([
            'client_id' => $client->id,
            'amount' => $request->input('amount'),
            'point' => intdiv($request->input('amount'), $lp->currency_value),
            'shop_id' => $request->input('shop_id'),
            'status' => 1,
            'currency' => $lp->currency,
            'area_manager_id' => $area_manager ? $area_manager->id : null
        ]);
        if ($lp->currency_value !== null && $lp->currency_value >= 1) {
            $client->clientLoyaltyProgram()->update([
                'point' => DB::raw('point+' . intdiv($request->input('amount'), $lp->currency_value))
            ]);
        }
        $client->logs()->create([
            'message' => 'Received ' . intdiv($request->input('amount'), $lp->currency_value) . ' points to card',
            'point' => intdiv($request->input('amount'), $lp->currency_value),
            'shop_id' => $request->input('shop_id'),
            'area_manager_id' => $area_manager ? $area_manager->id : null,
            'amount' => $request->input('amount'),
        ]);

        DB::commit();
        $client2 = clone $client;
        $client2->devices->map(function ($item) use ($request, $lp) {
            /** @var Device $item */
            if ($lp->currency_value !== null && $lp->currency_value >= 1) {
                Notify::sendNotification($item->token, 'NextCard', trans('manager/message.manager_sale_notification', ['points' => intdiv($request->input('amount'), $lp->currency_value)]), [
                    'actions' => 'card_scan',
                    'merchant_id' => auth()->id()
                ]);
            }
        });

        return $this->respond([
            'entity' => [
                'current_points' => $client->clientLoyaltyProgram()->first()->point,
                'points_added' => $lp->currency_value !== null && $lp->currency_value >= 1 ? intdiv($request->input('amount'), $lp->currency_value) : 0,
                'manager' => [
                    'name' => auth()->user()->name
                ],
                'client' => [
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name
                ],
            ],
            'message' => trans('manager/message.manager_sale'),
            'success' => true
        ]);
    }
}
