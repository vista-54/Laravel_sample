<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Offer\OfferStoreRequest;
use App\Http\Requests\Admin\Offer\OfferUpdateRequest;
use App\Http\Requests\Admin\OfferCard\OfferCardUpdateRequest;
use App\Http\Resources\Admin\Offer\OfferResource;
use App\Models\Client;
use App\Models\Device;
use App\Models\Offer;
use App\Models\OfferCard;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Admin\offer actions
 */
class OfferController extends ApiController
{

    /**
     * Display all loyalty program offers.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->respond([
            'entity' => auth()->user()->loyaltyProgram->offers()->paginate($request->input('limit', 10))
        ]);
    }

    /**
     * Create new loyalty program offer.
     *
     * @bodyParam name string required
     * @bodyParam description string
     * @bodyParam start_date string
     * @bodyParam end_date string
     * @bodyParam points_cost string required
     * @bodyParam customer_limit integer
     * @bodyParam availability_count string
     * @bodyParam notify string
     * @bodyParam status integer
     *
     * @param OfferStoreRequest $request
     * @param Offer $offer
     * @return Response
     */
    public function store(OfferStoreRequest $request, Offer $offer)
    {
        return $this->respondCreated(trans('admin/message.offer_create'), $offer->create($request->data()
            + [
                'loyalty_program_id' => auth()->user()->loyaltyProgram->id
            ]));
    }

    public function show(Offer $offer)
    {
        return $this->respond([
            'entity' => new OfferResource($offer)
        ]);
    }

    /**
     * Update offer.
     *
     * @bodyParam name string
     * @bodyParam description string
     * @bodyParam start_date string
     * @bodyParam end_date string
     * @bodyParam points_cost string
     * @bodyParam customer_limit integer
     * @bodyParam availability_count string
     * @bodyParam notify string
     * @bodyParam status integer
     *
     * @param OfferUpdateRequest $request
     * @param Offer $offer
     * @return Response
     */
    public function update(OfferUpdateRequest $request, Offer $offer)
    {
        $offer->update($request->data());
        return $this->respondCreated(trans('admin/message.offer_update'), $offer);
    }

    /**
     * Update offer card
     *
     * @param OfferCardUpdateRequest $request
     * @param OfferCard $card
     * @return \Illuminate\Http\JsonResponse
     */
    public function offerCardUpdate(OfferCardUpdateRequest $request, OfferCard $card)
    {
        $card->touch();
        $card->update($request->validated());
        return $this->respondCreated(trans('admin/message.offer_card_update'), $card);
    }

    /**
     * Block or unblock offer
     *
     * @param Offer $offer
     * @return \Illuminate\Http\JsonResponse
     */
    public function block(Offer $offer)
    {
        if ($offer->status === 1) {
            $offer->update(['status' => 0]);
            return $this->respondCreated(trans('admin/message.offer_locked'));
        } else {
            $offer->update(['status' => 1]);
            auth()->user()->clients->map(function ($item) use ($offer) {
                /** @var Client $item */
                $item->devices->map(function ($item) use ($offer) {
                    /** @var Device $item */
                    \Notify::sendNotification($item->token, 'NextCard', 'A new offer is available! ' . $offer->name, ['entity' => $offer, 'actions' => 'cupon_update'],'default', null, $offer->offerCard->icon);
                });
            });
            return $this->respondCreated(trans('admin/message.offer_unlocked'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Offer $offer
     * @return Response
     * @throws \Exception
     */
    public function destroy(Offer $offer)
    {
        $offer->delete();
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.offer_delete')
        ]);
    }
}
