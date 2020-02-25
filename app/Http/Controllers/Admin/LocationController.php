<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Location\LocationStoreRequest;
use App\Http\Requests\Admin\Location\LocationUpdateRequest;
use App\Models\Location;
use App\Models\LoyaltyProgram;
use Illuminate\Http\JsonResponse;

/**
 * @group Admin\location actions
 */
class LocationController extends ApiController
{

    /**
     * Return loyalty programs locations
     *
     * @return JsonResponse
     */
    public function index()
    {
        return $this->respond([
            'entity' => auth()->user()->loyaltyProgram->locations
        ]);
    }

    /**
     * Set new location.
     *
     * @bodyParam loyalty_program_id integer required
     * @bodyParam latitude string required
     * @bodyParam longitude string required
     * @bodyParam params string
     *
     * @param Location $location
     * @param LocationStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Location $location, LocationStoreRequest $request)
    {
        return $this->respondCreated(trans('admin/message.location_create'), $location->create($request->validated()));
    }

    /**
     * Display the specified resource.
     *
     * @param LoyaltyProgram $location
     * @return \Illuminate\Http\Response
     */
    public function show(LoyaltyProgram $location)
    {
        return $this->respond([
            'entity' => $location->locations
        ]);
    }

    /**
     * Update update location.
     *
     * @bodyParam loyalty_program_id integer required
     * @bodyParam latitude string
     * @bodyParam longitude string
     * @bodyParam params string
     *
     * @param Location $location
     * @param LocationUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(Location $location, LocationUpdateRequest $request)
    {
        $location->update($request->validated());
        return $this->respondCreated(trans('admin/message.location_update'), $location);
    }

    /**
     * Remove location.
     *
     * @param Location $location
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Location $location)
    {
        $location->delete();
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.location_delete')
        ]);
    }
}
