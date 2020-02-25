<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Manager\InviteRequest;
use App\Http\Requests\Manager\ScanRequest;
use App\Http\Resources\Collection;
use App\Http\Resources\Manager\Dropdown\OfferResource;
use App\Http\Resources\Manager\Dropdown\PassResource;
use App\Models\Client;
use App\Models\ClientShop;
use App\Models\Device;
use App\Models\Offer;
use App\Models\Pass;
use App\Models\Shop;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DB;

/**
 * @group Manager\scaner actions
 */
class ManagerController extends ApiController
{
    /**
     * Display shop list
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function shops(Request $request)
    {
        return $this->respond([
            'entity' => auth()->user()->shops()
                ->when($request->has('number'), function ($q) use ($request) {
                    /** @var Shop $q */
                    $q->where('number', $request->input('number+'));
                })
                ->get()
        ]);
    }

    /**
     * Request for manager scanner
     *
     * if type = 'loyalty'
     * @bodyParam type string required
     * @bodyParam shop_id integer
     * @bodyParam amount integer
     *
     * if type = 'offer' or 'coupon'
     * @bodyParam type string required
     * @bodyParam shop_id integer
     * @bodyParam card_id integer
     *
     * @param ScanRequest $request
     * @param Client $client
     * @return JsonResponse
     * @throws \Exception
     */
    public function scan(ScanRequest $request, Client $client)
    {
        if ($request->input('type') === ClientShop::TYPE_LOYALTY) {
            return $this->scanSale($request, $client);
        }
        if ($request->input('type') === ClientShop::TYPE_OFFER) {
            return $this->scanOffer($request, $client);
        }
        if ($request->input('type') === ClientShop::TYPE_COUPON) {
            return $this->scanCoupon($request, $client);
        }
        return $this->respond([
            'status' => false
        ]);
    }


    protected function scanSale(ScanRequest $request, Client $client)
    {
        DB::beginTransaction();
        $lp = $client->loyaltyProgram()->first();

        /** @var ClientShop $clientShop */
        $data = $client->clientShops()->create($request->validated()+ [
                'point' => DB::raw('point+' . intdiv($request->input('amount'), $lp->currency_value))
            ]);
        Transaction::create([
            'client_id' => $client->id,
            'amount' => $request->input('amount'),
            'point' => intdiv($request->input('amount'), $lp->currency_value),
            'shop_id' => $request->input('shop_id'),
            'status' => 1,
            'currency' => $lp->currency,
            'area_manager_id' => auth()->id()
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
            'area_manager_id' => auth()->id(),
            'amount' => $request->input('amount'),
        ]);

        DB::commit();
        $client2 = clone $client;
        $client2->devices->map(function ($item) use ($request, $lp) {
            /** @var Device $item */
            if ($lp->currency_value !== null && $lp->currency_value >= 1) {
                \Notify::sendNotification($item->token, 'NextCard', trans('manager/message.manager_sale_notification', ['points' => intdiv($request->input('amount'), $lp->currency_value)]), [
                    'actions' => 'card_scan',
                    'merchant_id' => auth()->user()->user->id
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


    protected function scanOffer(ScanRequest $request, Client $client)
    {
        DB::beginTransaction();
        $client2 = clone $client;
        if ($data = $client->offers()->where('offers.id', $request->input('card_id'))->updateExistingPivot($request->input('card_id'), ['used' => 1, 'shop_id' => $request->input('shop_id')])) {
            $client->logs()->create([
                'message' => 'Redeem offer ' . $client->offers()->where('offers.id', $request->input('card_id'))->first()->name,
                'point' => 0,
                'shop_id' => $request->input('shop_id'),
                'area_manager_id' => auth()->id(),
                'amount' => $request->input('amount') ?? 0,
            ]);
            DB::commit();
            $client2->devices->map(function ($item) {
                /** @var Device $item */
                \Notify::sendNotification($item->token, 'NextCard', trans('manager/message.manager_offer_notification'), [
                    'actions' => 'offer_scan',
                    'merchant_id' => auth()->user()->user->id
                ]);
            });
            return $this->respond([
                'message' => trans('manager/message.manager_offer_deactivate'),
                'success' => true
            ]);
        } else {
            DB::rollBack();
            return $this->respond([
                'message' => trans('manager/message.manager_offer_used'),
                'success' => false
            ]);
        }
    }


    protected function scanCoupon(ScanRequest $request, Client $client)
    {
        if (Pass::find($request->input('card_id'))->status === 0) {
            return $this->respond([
                'message' => trans('manager/message.manager_coupon_inactive'),
                'success' => false
            ]);
        }
        DB::beginTransaction();
        $client2 = clone $client;

        if ($client->passes()->wherePivot('pass_id', $request->input('card_id'))->doesntExist()) {
            $client->passes()->attach($request->input('card_id'));
            $client->passes()->updateExistingPivot($request->input('card_id'), [
                'shop_id' => $request->input('shop_id'),
                'created_by' => auth()->user()->id
            ]);
            $client->logs()->create([
                'message' => 'Redeem coupon ' . $client->passes()->where('id', $request->input('card_id'))->first()->title,
                'point' => 0,
                'shop_id' => $request->input('shop_id'),
                'area_manager_id' => auth()->id()
            ]);
            DB::commit();
            $client2->devices->map(function ($item) {
                /** @var Device $item */
                \Notify::sendNotification($item->token, 'NextCard', trans('manager/message.manager_coupon_used'), [
                    'actions' => 'coupon_scan',
                    'merchant_id' => auth()->user()->user->id
                ]);
            });
            return $this->respond([
                'message' => trans('manager/message.manager_coupon_used'),
                'success' => true
            ]);
        } else {
            DB::rollBack();
            return $this->respond([
                'message' => trans('manager/message.manager_coupon_already_used'),
                'success' => false
            ]);
        }
    }

    /**
     * @param Client $client
     * @return JsonResponse
     */
    public function client(Client $client)
    {
        return $this->respond([
            'entity' => $client
        ]);
    }

    /**
     * Request for manager phone input, like scanner
     *
     * if type = 'loyalty'
     * @bodyParam type string required
     * @bodyParam shop_id integer
     * @bodyParam amount integer
     *
     * if type = 'offer' or 'coupon'
     * @bodyParam type string required
     * @bodyParam shop_id integer
     * @bodyParam card_id integer
     *
     * @param ScanRequest $request
     * @param Client $client
     * @return JsonResponse
     * @throws \Exception
     */
    public function phone(ScanRequest $request, $clientPhone)
    {
        if ($request->input('type') === ClientShop::TYPE_LOYALTY) {
            return $this->phoneSale($request, $clientPhone);
        }
        if ($request->input('type') === ClientShop::TYPE_OFFER) {
            return $this->phoneOffer($request, $clientPhone);
        }
        if ($request->input('type') === ClientShop::TYPE_COUPON) {
            return $this->phoneCoupon($request, $clientPhone);
        }
        return $this->respond([
            'status' => false
        ]);
    }

    protected function phoneSale(ScanRequest $request, $phone)
    {
        if ($client = auth()->user()->user->clients()->where('phone', $phone)->first()) {
            /** @var Client $client */
            DB::beginTransaction();
            $lp = $client->loyaltyProgram()->first();

            /** @var ClientShop $clientShop */
            $client->clientShops()->create($request->validated() + [
                    'point' => DB::raw('point+' . intdiv($request->input('amount'), $lp->currency_value))
                ]);
            Transaction::create([
                'client_id' => $client->id,
                'amount' => $request->input('amount'),
                'point' => intdiv($request->input('amount'), $lp->currency_value),
                'shop_id' => $request->input('shop_id'),
                'status' => 1,
                'currency' => $lp->currency,
                'area_manager_id' => auth()->id()
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
                'area_manager_id' => auth()->id(),
                'amount' => $request->input('amount'),
            ]);

            DB::commit();
            $client2 = clone $client;
            $client2->devices->map(function ($item) use ($request, $lp) {
                /** @var Device $item */
                if ($lp->currency_value !== null && $lp->currency_value >= 1) {
                    \Notify::sendNotification($item->token, 'NextCard', trans('manager/message.manager_sale_notification', ['points' => intdiv($request->input('amount'), $lp->currency_value)]), [
                        'actions' => 'card_scan',
                        'merchant_id' => auth()->user()->user->id
                    ]);
                }
            });
        } else {
            abort(404, 'Client not found');
        }

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


    protected function phoneOffer(ScanRequest $request, $phone)
    {
        if ($client = auth()->user()->user->clients()->where('phone', $phone)->first()) {
            /** @var Client $client */
            DB::beginTransaction();
            $client2 = clone $client;
            if ($data = $client->offers()->where('offers.id', $request->input('card_id'))->updateExistingPivot($request->input('card_id'), ['used' => 1, 'shop_id' => $request->input('shop_id')])) {
                $client->logs()->create([
                    'message' => 'Redeem offer ' . $client->offers()->where('offers.id', $request->input('card_id'))->first()->name,
                    'point' => 0,
                    'shop_id' => $request->input('shop_id'),
                    'area_manager_id' => auth()->id(),
                    'amount' => $request->input('amount') ?? 0,
                ]);
                DB::commit();
                $client2->devices->map(function ($item) {
                    /** @var Device $item */
                    \Notify::sendNotification($item->token, 'NextCard', trans('manager/message.manager_offer_notification'), [
                        'actions' => 'offer_scan',
                        'merchant_id' => auth()->user()->user->id
                    ]);
                });
                return $this->respond([
                    'message' => trans('manager/message.manager_offer_deactivate'),
                    'success' => true
                ]);
            } else {
                DB::rollBack();
                return $this->respond([
                    'message' => trans('manager/message.manager_offer_used'),
                    'success' => false
                ]);
            }
        } else {
            abort(404, 'Client not found');
        }
    }


    protected function phoneCoupon(ScanRequest $request, $phone)
    {
        if ($client = auth()->user()->user->clients()->where('phone', $phone)->first()) {
            /** @var Client $client */
            if (Pass::find($request->input('card_id'))->status === 0) {
                return $this->respond([
                    'message' => trans('manager/message.manager_coupon_inactive'),
                    'success' => false
                ]);
            }
            DB::beginTransaction();
            $client2 = clone $client;

            if ($client->passes()->wherePivot('pass_id', $request->input('card_id'))->doesntExist()) {
                $client->passes()->attach($request->input('card_id'));
                $client->passes()->updateExistingPivot($request->input('card_id'), [
                    'shop_id' => $request->input('shop_id'),
                    'created_by' => auth()->user()->id
                ]);
                $client->logs()->create([
                    'message' => 'Redeem coupon ' . $client->passes()->where('id', $request->input('card_id'))->first()->title,
                    'point' => 0,
                    'shop_id' => $request->input('shop_id'),
                    'area_manager_id' => auth()->id()
                ]);
                DB::commit();
                $client2->devices->map(function ($item) {
                    /** @var Device $item */
                    \Notify::sendNotification($item->token, 'NextCard', trans('manager/message.manager_coupon_used'), [
                        'actions' => 'coupon_scan',
                        'merchant_id' => auth()->user()->user->id
                    ]);
                });
                return $this->respond([
                    'message' => trans('manager/message.manager_coupon_used'),
                    'success' => true
                ]);
            } else {
                DB::rollBack();
                return $this->respond([
                    'message' => trans('manager/message.manager_coupon_already_used'),
                    'success' => false
                ]);
            }
        } else {
            abort(404, 'Client not found');
        }
    }

    /**
     * Get data for offers and coupons dropdown
     *
     * @return JsonResponse
     */
    public function cardList()
    {
        $offers = auth()->user()->user->loyaltyProgram->offers()->where('status', Offer::ACTIVE)->get();
        $passes = auth()->user()->user->passes()->where('status', Offer::ACTIVE)->get();

        return $this->respond([
            'entity' => [
                'passes' => new Collection(PassResource::collection($passes)),
                'offers' => new Collection(OfferResource::collection($offers)),
            ]
        ]);
    }

    /**
     * @param $phone
     * @return JsonResponse
     */
    public function clientList($phone)
    {
        return $this->respond([
            'entity' => auth()->user()->user->clients()->where('phone', 'LIKE', '%' . $phone . '%')->limit(3)->get()
        ]);
    }
}
