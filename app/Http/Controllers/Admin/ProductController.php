<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\ClientShop;
use App\Models\Product;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;


/**
 * @group Admin\area-manager actions
 */
class ProductController extends ApiController
{
    public function productPerShop(Request $request)
    {
        return $this->respond([
            'entity' => auth()->user()->shops->map(function ($item) use ($request) {
                /** @var Shop $item */
                $item['products'] = Product::whereHas('clientShop', function ($q) use ($item, $request) {
                    /** @var ClientShop $q */
                    $q->where('shop_id', $item->id);
                })
                    ->when($request->has('from'), function ($q) use ($request) {
                        $q->where('created_at', '>=', Carbon::parse($request->input('from')));
                    })
                    ->when($request->has('to'), function ($q) use ($request) {
                        $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
                    })
                    ->get()
                    ->groupBy('name')->map(function ($item) {
                        $data['amount'] = $item[0]['value'] * $item->sum('quantity');
//                            . ' ' . auth()->user()->loyaltyProgram->currency;
                        $data['name'] = $item[0]['name'];
                        return $data;
                    })->values();
                return $item;
            })
        ]);
    }
}
