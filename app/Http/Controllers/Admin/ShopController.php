<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Shop\ShopStoreRequest;
use App\Http\Requests\Admin\Shop\ShopUpdateRequest;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Admin\Shop actions
 */
class ShopController extends ApiController
{
    /**
     * Display a listing of merchant shops.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->respond([
            'entity' => auth()->user()->shops()->orderBy('name')->paginate($request->input('limit'))
        ]);
    }

    /**
     * Create new Shop
     *
     * @bodyParam number integer required
     * @bodyParam name string required
     *
     * @param ShopStoreRequest $request
     * @param Shop $shop
     * @return Response
     */
    public function store(ShopStoreRequest $request, Shop $shop)
    {
        return $this->respondCreated(trans('admin/message.shop_create'), $shop->create($request->validated() + ['user_id' => auth()->id()]));
    }

    /**
     * Display the specified resource.
     *
     * @param Shop $shop
     * @return Response
     */
    public function show(Shop $shop)
    {
        return $this->respond([
            'entity' => $shop
        ]);
    }

    /**
     * Update Shop.
     *
     * @bodyParam number integer
     * @bodyParam name string
     *
     * @param ShopUpdateRequest $request
     * @param Shop $shop
     * @return Response
     */
    public function update(ShopUpdateRequest $request, Shop $shop)
    {
        $shop->update($request->validated());
        return $this->respondCreated(trans('admin/message.shop_update'), $shop);
    }

    /**
     * Remove shop.
     *
     * @param Shop $shop
     * @return Response
     * @throws \Exception
     */
    public function destroy(Shop $shop)
    {
        $shop->delete();
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.shop_delete')
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function shopsApi(Request $request)
    {
        return $this->respond([
            'entity' => auth()->user()->shops()
                ->when($request->has('id'), function ($item) use ($request) {
                    $item->where('id', $request->input('id'));
                })
                ->when($request->has('name'), function ($item) use ($request) {
                    $item->where('name', $request->input('name'));
                })
                ->when($request->has('city'), function ($item) use ($request) {
                    $item->where('city', $request->input('city'));
                })
                ->when($request->has('area'), function ($item) use ($request) {
                    $item->where('area', $request->input('area'));
                })
                ->when($request->has('region'), function ($item) use ($request) {
                    $item->where('region', $request->input('region'));
                })
                ->when($request->has('type'), function ($item) use ($request) {
                    $item->whereHas('shopType', function ($q) use ($request) {
                        $q->where('name', $request->input('type'));
                    });
                })
                ->when($request->has('cluster'), function ($item) use ($request) {
                    $item->where('cluster', $request->input('cluster'));
                })
                ->paginate($request->input('per_page', 20))
        ]);
    }
}
