<?php


namespace App\Http\Controllers\POS;


use App\Http\Controllers\ApiController;
use App\Http\Requests\POS\POSPhoneRequest;
use App\Http\Requests\POS\POSRequest;
use App\Models\Client;
use App\Models\ClientShop;
use App\Models\Device;
use App\Models\Offer;
use App\Models\Pass;
use App\Models\Transaction;
use DB;
use Illuminate\Http\JsonResponse;

class POSController extends ApiController
{
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
     * @param POSRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function scan(POSRequest $request)
    {
        if ($request->input('type') === ClientShop::TYPE_LOYALTY) {
            return $this->scanSale($request);
        }
        if ($request->input('type') === ClientShop::TYPE_OFFER) {
            return $this->scanOffer($request);
        }
        if ($request->input('type') === ClientShop::TYPE_COUPON) {
            return $this->scanCoupon($request);
        }
        return $this->respond([
            'status' => false
        ]);
    }

    protected function scanSale(POSRequest $request)
    {
        $client = Client::find($request->input('client_id'));
        DB::beginTransaction();
        $lp = $client->loyaltyProgram()->first();

        /** @var ClientShop $clientShop */
        $clientShop = $client->clientShops()->create($request->validated() + [
                'shop_id' => auth()->user()->shop_id,
                'point' => DB::raw('point+' . intdiv($request->input('amount'), $lp->currency_value))
            ]);
        $clientShop->poses()->create($request->validated());
        $clientShop->products()->createMany($request->input('items'));
        Transaction::create([
            'client_id' => $client->id,
            'amount' => $request->input('amount'),
            'point' => intdiv($request->input('amount'), $lp->currency_value),
            'shop_id' => auth()->user()->shop_id,
            'status' => 1,
            'currency' => $lp->currency,
        ]);
        if ($lp->currency_value !== null && $lp->currency_value >= 1) {
            $client->clientLoyaltyProgram()->update([
                'point' => DB::raw('point+' . intdiv($request->input('amount'), $lp->currency_value))
            ]);
        }
        $client->logs()->create([
            'message' => 'Received ' . intdiv($request->input('amount'), $lp->currency_value) . ' points to card',
            'point' => intdiv($request->input('amount'), $lp->currency_value),
            'shop_id' => auth()->user()->shop_id,
            'amount' => $request->input('amount'),
        ]);

        DB::commit();
        $client2 = clone $client;
        $client2->devices->map(function ($item) use ($request, $lp, $client) {
            /** @var Device $item */
            if ($lp->currency_value !== null && $lp->currency_value >= 1) {
                \Notify::sendNotification($item->token, 'NextCard', trans('manager/message.manager_sale_notification', ['points' => intdiv($request->input('amount'), $lp->currency_value)]), [
                    'actions' => 'card_scan',
                    'merchant_id' => $client->user->id
                ]);
            }
        });

        return $this->respond([
            'entity' => [
                'current_points' => $client->clientLoyaltyProgram()->first()->point,
                'points_added' => $lp->currency_value !== null && $lp->currency_value >= 1 ? intdiv($request->input('amount'), $lp->currency_value) : 0,
                'client' => [
                    'first_name' => $client->first_name,
                    'last_name' => $client->last_name
                ],
            ],
//            'message' => trans('manager/message.manager_sale'),
            'success' => true
        ]);
    }

    protected function scanOffer(POSRequest $request)
    {
        $client = Client::find($request->input('client_id'));

        DB::beginTransaction();
        $client2 = clone $client;
        if ($data = $client->offers()->where('offers.id', $request->input('offer_id'))->updateExistingPivot($request->input('offer_id'), ['used' => 1, 'shop_id' => auth()->user()->shop_id])) {
            Offer::find($request->input('offer_id'))->poses()->create($request->validated());
            $client->logs()->create([
                'message' => 'Redeem offer ' . $client->offers()->where('offers.id', $request->input('offer_id'))->first()->name,
                'point' => 0,
                'shop_id' => auth()->user()->shop_id,
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

    protected function scanCoupon(POSRequest $request)
    {
        $client = Client::find($request->input('client_id'));

        if (Pass::find($request->input('coupon_id'))->status === 0) {
            return $this->respond([
                'message' => trans('manager/message.manager_coupon_inactive'),
                'success' => false
            ]);
        }
        DB::beginTransaction();
        $client2 = clone $client;

        if ($client->passes()->wherePivot('pass_id', $request->input('coupon_id'))->doesntExist()) {
            $client->passes()->attach($request->input('coupon_id'));
            $client->passes()->updateExistingPivot($request->input('coupon_id'), [
                'shop_id' => auth()->user()->shop_id,
                'created_by' => auth()->user()->id
            ]);
            Pass::find($request->input('coupon_id'))->poses()->create($request->validated());
            $client->logs()->create([
                'message' => 'Redeem coupon ' . $client->passes()->where('id', $request->input('coupon_id'))->first()->title,
                'point' => 0,
                'shop_id' => auth()->user()->shop_id,
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
     * @param POSPhoneRequest $request
     * @param Client $client
     * @return JsonResponse
     * @throws \Exception
     */
    public function phone(POSPhoneRequest $request)
    {
        if ($request->input('type') === ClientShop::TYPE_LOYALTY) {
            return $this->phoneSale($request);
        }
        if ($request->input('type') === ClientShop::TYPE_OFFER) {
            return $this->phoneOffer($request);
        }
        if ($request->input('type') === ClientShop::TYPE_COUPON) {
            return $this->phoneCoupon($request);
        }
        return $this->respond([
            'status' => false
        ]);
    }

    protected function phoneSale(POSPhoneRequest $request)
    {
        if ($client = auth()->user()->user->clients()->where('phone', $request->input('phone'))->first()) {
            /** @var Client $client */
            DB::beginTransaction();
            $lp = $client->loyaltyProgram()->first();

            /** @var ClientShop $clientShop */
            $clientShop = $client->clientShops()->create($request->validated() + [
                    'shop_id' => auth()->user()->shop_id,
                    'point' => DB::raw('point+' . intdiv($request->input('amount'), $lp->currency_value))
                ]);
            $clientShop->poses()->create($request->validated());
            $clientShop->products()->createMany($request->input('items'));
            Transaction::create([
                'client_id' => $client->id,
                'amount' => $request->input('amount'),
                'point' => intdiv($request->input('amount'), $lp->currency_value),
                'shop_id' => auth()->user()->shop_id,
                'status' => 1,
                'currency' => $lp->currency,
            ]);
            if ($lp->currency_value !== null && $lp->currency_value >= 1) {
                $client->clientLoyaltyProgram()->update([
                    'point' => DB::raw('point+' . intdiv($request->input('amount'), $lp->currency_value))
                ]);
            }
            $client->logs()->create([
                'message' => 'Received ' . intdiv($request->input('amount'), $lp->currency_value) . ' points to card',
                'point' => intdiv($request->input('amount'), $lp->currency_value),
                'shop_id' => auth()->user()->shop_id,
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
//            'message' => trans('manager/message.manager_sale'),
            'success' => true
        ]);
    }

    protected function phoneOffer(POSPhoneRequest $request)
    {
        if ($client = auth()->user()->user->clients()->where('phone', $request->input('phone'))->first()) {
            /** @var Client $client */
            DB::beginTransaction();
            $client2 = clone $client;
            if ($data = $client->offers()->where('offers.id', $request->input('offer_id'))->updateExistingPivot($request->input('offer_id'), ['used' => 1, 'shop_id' => auth()->user()->shop_id])) {
                Offer::find($request->input('offer_id'))->poses()->create($request->validated());
                $client->logs()->create([
                    'message' => 'Redeem offer ' . $client->offers()->where('offers.id', $request->input('offer_id'))->first()->name,
                    'point' => 0,
                    'shop_id' => auth()->user()->shop_id,
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

    protected function phoneCoupon(POSPhoneRequest $request)
    {
        if ($client = auth()->user()->user->clients()->where('phone', $request->input('phone'))->first()) {
            /** @var Client $client */
            if (Pass::find($request->input('coupon_id'))->status === 0) {
                return $this->respond([
                    'message' => trans('manager/message.manager_coupon_inactive'),
                    'success' => false
                ]);
            }
            DB::beginTransaction();
            $client2 = clone $client;

            if ($client->passes()->wherePivot('pass_id', $request->input('coupon_id'))->doesntExist()) {
                $client->passes()->attach($request->input('coupon_id'));
                $client->passes()->updateExistingPivot($request->input('coupon_id'), [
                    'shop_id' => auth()->user()->shop_id,
                    'created_by' => auth()->user()->id
                ]);
                Pass::find($request->input('coupon_id'))->poses()->create($request->validated());
                $client->logs()->create([
                    'message' => 'Redeem coupon ' . $client->passes()->where('id', $request->input('coupon_id'))->first()->title,
                    'point' => 0,
                    'shop_id' => auth()->user()->shop_id,
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
}
