<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Client\Pass\ClientPassResource;
use App\Http\Resources\Collection;
use App\Models\Pass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @group Client\coupon actions
 */
class PassController extends ApiController
{

    /**
     * Display client active coupons
     *
     * @return JsonResponse
     */
    public function index()
    {
        return $this->respond([
            'entity' => new Collection(ClientPassResource::collection(auth()->user()->user->passes()->where('status', Pass::ACTIVE)->get()))
        ]);
    }
}
