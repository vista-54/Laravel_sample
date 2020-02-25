<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Dashboard\AbcRequest;
use App\Http\Requests\Admin\Dashboard\NewReturningRequest;
use App\Http\Requests\Admin\Dashboard\RetentionRequest;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;

class BusinessRulesController extends ApiController
{
    /** @var User user */
    private $user;
    private $abc;
    private $retention;
    private $newReturning;

    public function __construct()
    {
        parent::__construct();
        $this->user = auth()->user();
    }


    public function segmentation()
    {
        return $this->respond([
            'entity' => [
                'abc' => [
                    $this->abcFilter('Top Customer', 'top_customer', 50, 7),
                    $this->abcFilter('Bargain Customer', 'bargain_customer', 40, 7),
                    $this->abcFilter('Upsale Customer', 'upsale_customer', 30, 7),
                    $this->abcFilter('Occasional Customer', 'occasional_customer', 20, 7),
                    $this->abcFilter('Weak Customer', 'weak_customer', 10, 7, false),
                ],
                'retention' => [
                    $this->retentionFilter('Lost Transaction', 'lost_transaction', 0, 180, false),
                    $this->retentionFilter('Inactive Transaction', 'inactive_transaction', 0, 30, true),
                    $this->retentionFilter('Uncertain Transaction', 'uncertain_transaction', 1, 180, true),
                    $this->retentionFilter('Overaging Transaction', 'overaging_transaction', 1, 90, true),
                    $this->retentionFilter('New Transaction', 'new_transaction', 1, 7, false),

                ],
                'new_returning' => [
                    $this->new('New Customer', 'new_customer', 1, 7),
                    $this->returning('Returning Customer', 'returning_customer', 2, 7),
                ]
            ]
        ]);
    }

    public function abc(AbcRequest $request)
    {
        return $this->respond([
            $this->abcFilter($request->input('0.label', 'Top Customer'),
                $request->input('0.name', 'top_customer'),
                $request->input('0.value', 50),
                $request->input('0.period', 7)),
            $this->abcFilter($request->input('1.label', 'Bargain Customer'),
                $request->input('1.name', 'bargain_customer'),
                $request->input('1.value', 50),
                $request->input('1.period', 7)),
            $this->abcFilter($request->input('2.label', 'Upsale Customer'),
                $request->input('2.name', 'upsale_customer'),
                $request->input('2.value', 50),
                $request->input('2.period', 7)),
            $this->abcFilter($request->input('3.label', 'Occasional Customer'),
                $request->input('3.name', 'occasional_customer'),
                $request->input('3.value', 50),
                $request->input('3.period', 7)),
            $this->abcFilter($request->input('4.label', 'Weak Customer'),
                $request->input('4.name', 'weak_customer'),
                $request->input('4.value', 50),
                $request->input('4.period', 7)),
        ]);
    }

    public function retention(RetentionRequest $request)
    {
        return $this->respond([
            $this->retentionFilter(
                $request->input('0.label', 'Lost Transaction'),
                $request->input('0.name', 'lost_transaction'),
                $request->input('0.value', 0),
                $request->input('0.period', 180),
                false),
            $this->retentionFilter(
                $request->input('1.label', 'Inactive Transaction'),
                $request->input('1.name', 'inactive_transaction'),
                $request->input('1.value', 0),
                $request->input('1.period', 30),
                true),
            $this->retentionFilter(
                $request->input('2.label', 'Uncertain Transaction'),
                $request->input('2.name', 'uncertain_transaction'),
                $request->input('2.value', 1),
                $request->input('2.period', 180),
                true),
            $this->retentionFilter(
                $request->input('3.label', 'Overaging Transaction'),
                $request->input('3.name', 'overaging_transaction'),
                $request->input('3.value', 1),
                $request->input('3.period', 90),
                true),
            $this->retentionFilter(
                $request->input('4.label', 'New Transaction'),
                $request->input('4.name', 'new_transaction'),
                $request->input('4.value', 1),
                $request->input('4.period', 7),
                false),
        ]);
    }

    public function newReturning(NewReturningRequest $request)
    {
        return $this->respond([
            $this->new(
                $request->input('0.label', 'New Customer'),
                $request->input('0.name', 'new_customer'),
                $request->input('0.value', 1),
                $request->input('0.period', 7)),
            $this->returning(
                $request->input('1.label', 'Returning Customer'),
                $request->input('1.name', 'returning_customer'),
                $request->input('1.value', 2),
                $request->input('1.period', 7)),
        ]);
    }

    private function abcFilter($label, $name, $segment, $period, $more = true)
    {
        $clients = $this->user->clients;

        $result = $clients->filter(function ($client) use ($segment, $period, $more) {
            /** @var Client $client */
            $spend = $client->clientShops()
                ->where('created_at', '>=', Carbon::now()->subDays($period))
                ->sum('amount');
            if ($more) {
                return $spend > $segment;
            } else {
                return $spend < $segment;
            }
        })->count();

        return [
            'label' => $label,
            'name' => $name,
            'result' => $result,
            'period' => $period
        ];
    }

    private function retentionFilter($label, $name, $segment, $period, $more = true)
    {
        $clients = $this->user->clients;
        $result = $clients->filter(function ($client) use ($segment, $period, $more) {
            /** @var Client $client */
            $spend = $client->clientShops()
                ->where('created_at', '>=', Carbon::now()->subDays($period))
                ->count();
            if ($more) {
                return $spend > $segment;
            } else {
                return $spend < $segment;
            }
        })->count();
        return [
            'label' => $label,
            'name' => $name,
            'result' => $result,
            'period' => $period
        ];
    }

    private function new($label, $name, $segment, $period)
    {
        $clients = $this->user->clients;
        $result = $clients->filter(function ($client) use ($segment, $period) {
            /** @var Client $client */
            $spend = $client->clientShops()
                ->where('created_at', '>=', Carbon::now()->subDays($period))
                ->sum('amount');
            return $segment == $spend;
        })->count();

        return [
            'label' => $label,
            'name' => $name,
            'result' => $result,
            'period' => $period
        ];
    }

    private function returning($label, $name, $segment, $period)
    {
        $clients = $this->user->clients;

        $result = $clients->filter(function ($client) use ($segment, $period) {
            /** @var Client $client */
            $spend = $client->clientShops()
                ->where('created_at', '>=', Carbon::now()->subDays($period))
                ->sum('amount');
            return $segment < $spend;
        })->count();

        return [
            'label' => $label,
            'name' => $name,
            'result' => $result,
            'period' => $period
        ];
    }

}
