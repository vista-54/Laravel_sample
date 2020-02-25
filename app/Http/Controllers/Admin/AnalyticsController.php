<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\ApiController;
use App\Http\Resources\Admin\Campaign\AnalyticsResource;
use App\Http\Resources\Collection;
use App\Models\Campaign;
use Illuminate\Http\Request;

class AnalyticsController extends ApiController
{
    public function data(Campaign $campaign, Request $request)
    {
        $data = Campaign::paginate($request->input('limit', 10));
        return $this->respondWithPagination($data, new Collection(AnalyticsResource::collection($data)));
    }
}
