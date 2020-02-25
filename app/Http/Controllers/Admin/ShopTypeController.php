<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\ShopType;
use Illuminate\Http\Request;

class ShopTypeController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->respond([
            'entity' => ShopType::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\StoreType  $storeType
     * @return \Illuminate\Http\Response
     */
    public function show(ShopType $storeType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Model\StoreType  $storeType
     * @return \Illuminate\Http\Response
     */
    public function edit(ShopType $storeType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\StoreType  $storeType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShopType $storeType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\StoreType  $storeType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShopType $storeType)
    {
        //
    }
}
