<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Manager\Profile\ProfilePasswordUpdateRequest;
use App\Http\Requests\Manager\Profile\ProfileUpdateRequest;
use App\Models\ClientShop;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Manager\profile actions
 */
class ProfileController extends ApiController
{
    /**
     * Display manager data.
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
     * Update manager data.
     *
     * @param ProfileUpdateRequest $request
     * @return Response
     */
    public function update(ProfileUpdateRequest $request)
    {
        auth()->user()->update($request->validated());
        return $this->respondCreated(trans('manager/message.profile_update'), auth()->user());
    }

    /**
     * Update manager password
     *
     * @param ProfilePasswordUpdateRequest $request
     * @return JsonResponse
     * @throws \ErrorException
     */
    public function updatePassword(ProfilePasswordUpdateRequest $request)
    {
        if (\Hash::check($request->input('old_password'), auth()->user()->password)) {
            return $this->respondCreated(trans('manager/message.profile_pass_update'), auth()->user()->update([
                'password' => \Hash::make($request->input('password'))
            ]));
        } else {
            throw new \ErrorException(trans('manager/message.profile_wrong_pass'), 422);
        }
    }

    /**
     * Display data for manager
     *
     * @return JsonResponse
     */
    public function logo()
    {
        /** @var User $user */
        $user = auth()->user()->user;
        $clientShop = ClientShop::where('created_by', auth()->user()->id)->latest()->first();
        $shop = $user->shops()->count();

        return $this->respond([
            'entity' => $user->logo,
            'merchant_name' => $user->full_name,
            'currency' => $user->loyaltyProgram()->first()->currency,
            'last_shop' => $shop === 1 ? $user->shops()->first() : ($clientShop ? $clientShop->shop : null)
        ]);
    }
}
