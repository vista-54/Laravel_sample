<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Resources\Admin\Transaction\TransactionListResource;
use App\Http\Resources\Collection;
use App\Models\Client;
use App\Models\ClientShop;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


/**
 * @group Admin\area-manager actions
 */
class TransactionController extends ApiController
{
    protected function transactions()
    {
        return Transaction::whereHas('client', function ($item) {
            /** @var Client $item */
            $item->where('user_id', auth()->id());
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transactionList(Request $request)
    {
        $transactionList = $this->transactions()
            ->when($request->has('from') && $request->input('from') != '', function ($q) use ($request) {
            /** @var Transaction $q */
            $q->where('created_at', '>=', Carbon::parse($request->input('from')));
        })
            ->when($request->has('to') && $request->input('to') != '', function ($q) use ($request) {
                /** @var Transaction $q */
                $q->where('created_at', '<=', Carbon::parse($request->input('to'))->addDay());
            })->paginate($request->input('limit', 20));

        return $this->respondWithPagination($transactionList, new Collection(TransactionListResource::collection($transactionList)));
    }

    public function transactionChart()
    {
        return $this->respond([
            'new_buyers' => $this->getBuyers(),
            'average_billing' => $this->getAverageBilling(),
            'top_venues' => $this->getTopVenues(),
            'total_amount' => $this->totalAmount()
        ]);
    }

    /**
     * @return array
     */
    protected function getBuyers()
    {
        /** @var User $user  */
        $user = auth()->user();
        return [
            'this_week' => $user->clients()->whereHas('transactions', function ($q) {
                /** @var Transaction $q */
                $q->where('created_at', '>', Carbon::now()->startOfWeek()->toDateString());
            })->count(),
            'previous_week' => $user->clients()->whereHas('transactions', function ($q) {
                /** @var Transaction $q */
                $q->where('created_at', '>', Carbon::now()->subWeek()->startOfWeek()->toDateString());
            })->count(),
            'this_month' => $user->clients()->whereHas('transactions', function ($q) {
                /** @var Transaction $q */
                $q->where('created_at', '>', Carbon::now()->startOfMonth()->toDateString());
            })->count(),
            'previous_month' => $user->clients()->whereHas('transactions', function ($q) {
                /** @var Transaction $q */
                $q->where('created_at', '>', Carbon::now()->subMonth()->startOfMonth()->toDateString());
            })->count(),
        ];
    }

    protected function getAverageBilling()
    {
        return [
            'this_week' => $this->transactions()
                ->where('created_at', '>', Carbon::now()->startOfWeek()->toDateString())
                ->average('amount'),
            'previous_week' => $this->transactions()
                ->where('created_at', '>', Carbon::now()->subWeek()->startOfWeek()->toDateString())
                ->average('amount'),
            'this_month' => $this->transactions()
                ->where('created_at', '>', Carbon::now()->startOfMonth()->toDateString())
                ->average('amount'),
            'previous_month' => $this->transactions()
                ->where('created_at', '>', Carbon::now()->subYear()->startOfMonth()->toDateString())
                ->average('amount'),
        ];
    }


    protected function getTopVenues()
    {
        return auth()->user()->shops()
            ->withCount('clientShops')
            ->orderBy('client_shops_count', 'desc')
            ->limit(7)
            ->get();
    }

    protected function totalAmount()
    {

        return number_format($this->transactions()->sum('amount'), 0, '.', ',') . ' ' . auth()->user()->loyaltyProgram->currency;
    }
}
