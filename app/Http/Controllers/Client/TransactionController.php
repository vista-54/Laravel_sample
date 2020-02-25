<?php

namespace App\Http\Controllers\Client;


use App\Http\Controllers\ApiController;
use Illuminate\Http\Response;

/**
 * @group Client\transaction actions
 */
class TransactionController extends ApiController
{
    /**
     * Display client transaction.
     *
     * @return Response
     */
    public function index()
    {
        return $this->respond([
//            'entity' => new Collection(TransactionResource::collection(auth()->user()->transactions()->get()))
            'entity' => auth()->user()->transactions()->get()
        ]);
    }
}
