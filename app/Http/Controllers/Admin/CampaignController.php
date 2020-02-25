<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Campaign\CampaignUpdateRequest;
use App\Http\Requests\Admin\Campaign\SetScheduleRequest;
use App\Http\Resources\Admin\Campaign\CampaignResource;
use App\Http\Resources\Collection;
use App\Models\Campaign;
use App\Models\Client;
use App\Models\ClientShop;
use App\Models\Invite;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CampaignController extends ApiController
{
    /**
     * Display campaign listing.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $data = auth()->user()->campaigns()->paginate($request->input('limit', 10));
        return $this->respondWithPagination($data, new Collection(CampaignResource::collection($data)));
    }

    public function show(Campaign $campaign)
    {
        return $this->respond([
            'entity' => new CampaignResource($campaign)
        ]);
    }

    /**
     * Update existing campaign
     *
     * @param Campaign $campaign
     * @param CampaignUpdateRequest $request
     * @return JsonResponse
     */
    public function update(Campaign $campaign, CampaignUpdateRequest $request)
    {
        $campaign->update($request->data());
        $campaign->shops()->sync($request->input('shop_id'));
        return $this->respondCreated('Campaign updated', $campaign);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Campaign $campaign
     * @return Response
     * @throws \Exception
     */
    public function destroy(Campaign $campaign)
    {
        return  $this->respondCreated($campaign->delete());
    }


    /**
     * Set datetime for campaign schedule
     *
     * @param Campaign $campaign
     * @param SetScheduleRequest $request
     * @return JsonResponse
     */
    public function setSchedule(Campaign $campaign, SetScheduleRequest $request)
    {
//        return $this->respond([
//            'data' => Carbon::now()->startOfHour()->toDateTimeString()
//        ]);
//        return $this->respond(Carbon::now()->timezone("-2")->toDateTimeString());
        return $this->respondCreated($campaign->update($request->validated()));
    }

    public function analytics(Campaign $campaign)
    {

    }
}
