<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Card\CardUpdateRequest;
use App\Models\Card;
use App\Models\Client;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @group Admin\card actions
 */
class CardController extends ApiController
{

    /**
     * Get users loyalty program stamps
     *
     * @return JsonResponse
     */
    public function index()
    {
        return $this->respond([
            'entity' => auth()->user()->loyaltyProgram->card
        ]);
    }
    /**
     * Display card data.
     *
     * @param Card $card
     * @return Response
     */
    public function show(Card $card)
    {
        return $this->respond([
            'entity' => $card
        ]);
    }

    /**
     * Update loyalty program card.
     *
     * @bodyParam background_color string Set background color
     * @bodyParam background_main_color string Set background main color
     * @bodyParam foreground_color string Set foreground color
     * @bodyParam label_color string Set label color
     * @bodyParam points_head string Set points head
     * @bodyParam points_value string Set points value
     * @bodyParam customer_head string Set customer head
     * @bodyParam customer_value string Set customer value
     * @bodyParam flip_head string Set flip head
     * @bodyParam flip_value string Set flip value
     * @bodyParam loyalty_profile string Set loyalty profile
     * @bodyParam loyalty_offers string Set loyalty offers
     * @bodyParam loyalty_contact string Set loyalty contact
     * @bodyParam loyalty_terms string Set loyalty terms
     * @bodyParam loyalty_terms_value string Set loyalty terms value
     * @bodyParam loyalty_message string Set loyalty message
     * @bodyParam customer_id string Set customer_id
     * @bodyParam icon string Set icon
     * @bodyParam background_image string Set background image
     *
     * @param Card $card
     * @param CardUpdateRequest $request
     * @return Response
     */
    public function update(Card $card, CardUpdateRequest $request)
    {
        $card->touch();
        $card->update($request->validated());
//        auth()->user()->clients->map(function ($item) use ($card) {
//            /** @var Client $item */
//            $item->devices->map(function ($item) use ($card) {
//                /** @var Device $item */
//                \Notify::sendNotification($item->token, 'NextCard', 'Card updated', ['entity' => $card]);
//            });
//        });
        return $this->respondCreated(trans('admin/message.card_update'), $card);
    }
}
