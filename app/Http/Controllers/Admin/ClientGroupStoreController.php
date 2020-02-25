<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\ClientGroup\ClientGroupStoreRequest;
use App\Http\Requests\Admin\ClientGroup\ClientGroupUpdateRequest;
use App\Models\Client;
use App\Models\ClientGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ClientGroupStoreController extends ApiController
{
    /** @var User user */
    private $user;
    private $ids = [];

    public function __construct()
    {
        parent::__construct();
        $this->user = auth()->user();
    }

    /**
     * @param ClientGroupStoreRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(ClientGroupStoreRequest $request)
    {
        \DB::beginTransaction();
        /** @var ClientGroup $clientGroup */
        $clientGroup = auth()->user()->clientGroups()->create($request->validated());
        $this->filters($request->input('filters'));
        $clientGroup->clients()->sync(collect($this->ids)->unique()->values()->all());
        \DB::commit();
        return $this->respondCreated(trans('admin/message.group_create'), $clientGroup);
    }

    private function filters($filters)
    {
        foreach ($filters as $k => $v) {
            switch ($k) {
                case 'abc':
                    foreach ($v as $one) {
                        if ($one['name'] === 'weak_customer') {
                            $this->abcFilter($one['value'], $one['period'], false);
                        } else {
                            $this->abcFilter($one['value'], $one['period']);
                        }
                    }
                    break;
                case 'retention':
                    foreach ($v as $one) {
                        $this->retentionFilter($one['value'], $one['period']);
                    }
                    break;
                case 'new_returning':
                    foreach ($v as $one) {
                        if ($one['name'] == 'new_customer') {
                            $this->new($one['value'], $one['period']);
                        } elseif ($one['name'] == 'returning_customer') {
                           $this->returning($one['value'], $one['period']);
                        }
                    }
                    break;
            }
        }
    }

    private function abcFilter($segment, $period, $more = true)
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
        })->map(function ($item) {
            return $item->id;
        })->all();
        if ($result) {
            $this->ids = array_merge($this->ids, $result);
        }
    }

    private function retentionFilter($segment, $period, $more = true)
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
        })->map(function ($item) {
            return $item->id;
        })->all();
        if ($result) {
            $this->ids = array_merge($this->ids, $result);
        }
    }

    private function new($segment, $period)
    {
        $clients = $this->user->clients;
        $result = $clients->filter(function ($client) use ($segment, $period) {
            /** @var Client $client */
            $spend = $client->clientShops()
                ->where('created_at', '>=', Carbon::now()->subDays($period))
                ->sum('amount');
            return $segment == $spend;
        })->map(function ($item) {
            return $item->id;
        })->all();
        if ($result) {
            $this->ids = array_merge($this->ids, $result);
        }
    }

    private function returning($segment, $period)
    {
        $clients = $this->user->clients;

        $result = $clients->filter(function ($client) use ($segment, $period) {
            /** @var Client $client */
            $spend = $client->clientShops()
                ->where('created_at', '>=', Carbon::now()->subDays($period))
                ->sum('amount');
            return $segment < $spend;
        })->map(function ($item) {
            return $item->id;
        })->all();
        if ($result) {
            $this->ids = array_merge($this->ids, $result);
        }
    }
}
