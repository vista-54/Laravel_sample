<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Client\LoyaltyProgram\LoyaltyProgramResource;
use App\Http\Resources\Client\Offer\ClientOfferResource;
use App\Http\Resources\Collection;
use App\Models\Client;
use App\Models\Device;
use App\Models\Offer;
use App\Models\Transaction;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @group Client\loyalty-program actions
 */
class LoyaltyProgramController extends ApiController
{
    /**
     * Display clients loyalty program.
     *
     * @return Response
     */
    public function loyaltyProgram()
    {
        return $this->respond([
            'entity' => new LoyaltyProgramResource(auth()->user()->user->loyaltyProgram)
        ]);
    }

    /**
     * Display available offers
     *
     * @return JsonResponse
     */
    public function offers()
    {
        return $this->respond([
            'entity' => new Collection(ClientOfferResource::collection(auth()->user()->user->loyaltyProgram->offers()->where('status', Offer::ACTIVE)->get()))
        ]);
    }

    /**
     * Display clients active offer
     *
     * @return JsonResponse
     */
    public function offerShow()
    {
        return $this->respond([
            'entity' => auth()->user()->offers()->wherePivot('used', 0)->first() ? new ClientOfferResource(auth()->user()->offers()->wherePivot('used', 0)->first()) : null
        ]);
    }

    /**
     * Display, does client have active offer
     *
     * @return JsonResponse
     */
    public function checkActiveOffer()
    {
        return $this->respond([
            'entity' => auth()->user()->offers()->wherePivot('used', 0)->first() ? true : false
        ]);
    }

    /**
     * Display offer locations
     *
     * @return JsonResponse
     */
    public function offerLocations(Offer $offer)
    {
        return $this->respond([
            'entity' => $offer->offerLocations
        ]);
    }

    /**
     * Buying offer
     *
     * @param Offer $offer
     * @return int
     * @throws \Exception
     */
    public function buy(Offer $offer)
    {
        /** @var Client $client */
        $client = auth()->user();

        if ($client->offers()->wherePivot('used', 0)->exists()) {
            throw new \Exception('You can have only one active offer.', '401');
        }
        DB::beginTransaction();
        $client->offers()->attach($offer->id, ['created_by' => $client->id]);
        Transaction::create([
            'client_id' => $client->id,
            'point' => $offer->points_cost,
            'shop_name' => 'Buy offer',
            'status' => 0
        ]);
        if ($client->clientLoyaltyProgram->point >= $offer->points_cost) {

            auth()->user()->devices->map(function ($item) use ($offer) {
                /** @var Device $item */
                \Notify::sendNotification($item->token, 'NextCard', 'Your offer ' . $offer->name . ' is ready', ['entity' => $offer, 'actions' => 'offer_update']);
            });
            $client->increment('lifetime_value', $offer->points_cost);
            DB::commit();
            auth()->user()->logs()->create([
                'message' => 'Use offer ' . $offer->name,
                'point' => '-' . $offer->points_cost
            ]);

            return $this->respond([
                'entity' => $client->clientLoyaltyProgram()->update(['point' => $client->clientLoyaltyProgram->point - $offer->points_cost]),
                'message' => trans('client/message.loyalty_buy_success')
            ]);

        } else {
            DB::rollBack();
            throw new \Exception('You have not enough points to use this offer', '401');
        }
//        }

    }

    /**
     * Show loyalty program terms
     *
     * @return JsonResponse
     */
    public function termsConditions()
    {
        return $this->respond([
            'entity' => auth()->user()->user->loyaltyProgram->contactsTerm
        ]);
    }
}
