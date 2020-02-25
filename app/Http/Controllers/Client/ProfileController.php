<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Client\Client\ProfilePasswordUpdateRequest;
use App\Http\Requests\Client\Client\ProfileUpdateRequest;
use App\Models\Offer;
use App\Models\Pass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Client\profile actions
 */
class ProfileController extends ApiController
{
    /**
     * Display client data
     *
     * @return Response
     */
    public function index()
    {
        return $this->respond([
            'entity' => auth()->user()
        ]);
    }

    /**
     * Update client data.
     *
     * @bodyParam first_name string
     * @bodyParam last_name string
     * @bodyParam email string
     * @bodyParam phone string
     * @bodyParam address string
     * @bodyParam timezone string
     * @bodyParam birthday string
     * @bodyParam race string
     * @bodyParam country_code string
     *
     * @param ProfileUpdateRequest $request
     * @return Response
     */
    public function update(ProfileUpdateRequest $request)
    {
        auth()->user()->update($request->validated());
        return $this->respondCreated(trans('client/message.profile_update'), auth()->user());
    }

    /**
     * Update password
     *
     * @bodyParam old_password string required
     * @bodyParam password string required
     *
     * @param ProfilePasswordUpdateRequest $request
     * @return JsonResponse
     * @throws \ErrorException
     */
    public function updatePassword(ProfilePasswordUpdateRequest $request)
    {
        if (\Hash::check($request->input('old_password'), auth()->user()->password)) {
            return $this->respondCreated(trans('client/message.profile_pass_update'), auth()->user()->update([
                'password' => \Hash::make($request->input('password'))
            ]));
        } else {
            throw new \ErrorException(trans('client/message.profile_wrong_pass'), 422);
        }
    }

    /**
     * Display shop list
     *
     * @return JsonResponse
     */
    public function shops()
    {
        $offerLocations = auth()->user()->user->loyaltyProgram->offers->map(function ($item) {
            /** @var Offer $item */
            return $item->offerLocations;
        })->flatten();
        $offerFlat = $offerLocations->all();

        $passLocations = auth()->user()->user->passes->map(function ($item) {
            /** @var Pass $item */
            return $item->passLocations;
        })->flatten();
        $passLoyalty = auth()->user()->user->loyaltyProgram->locations->toArray();
        $passFlat = $passLocations->all();
        return $this->respond([
            'entity' => array_merge($offerFlat, $passFlat, $passLoyalty)
//            'entity' => $offerFlat + $passFlat
        ]);
    }
}
