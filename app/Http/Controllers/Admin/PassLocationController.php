<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\PassLocation\PassLocationStoreRequest;
use App\Http\Requests\Admin\PassLocation\PassLocationUpdateRequest;
use App\Models\Pass;
use App\Models\PassLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @group Admin\coupon-location actions
 */
class PassLocationController extends ApiController
{
    /**
     * Display pass locations by pass_id
     *
     * @param Pass $pass
     * @return JsonResponse
     */
    public function getTemplate(Pass $pass)
    {
        return $this->respond([
            'entity' => $pass->passLocations
        ]);
    }

    /**
     * Store create new pass location.
     *
     * @bodyParam pass_id integer required
     * @bodyParam latitude string required
     * @bodyParam longitude string required
     * @bodyParam params string
     *
     * @param PassLocationStoreRequest $request
     * @param PassLocation $passLocation
     * @return Response
     */
    public function store(PassLocationStoreRequest $request, PassLocation $passLocation)
    {
        return $this->respondCreated(trans('admin/message.pass_location_create'), $passLocation->create($request->validated()));
    }

    /**
     * Display the specified resource.
     *
     * @param PassLocation $passLocation
     * @return Response
     */
    public function show(PassLocation $passLocation)
    {
        return $this->respond([
            'entity' => $passLocation
        ]);
    }

    /**
     * Update pass location.
     *
     * @bodyParam pass_id integer
     * @bodyParam latitude string
     * @bodyParam longitude string
     * @bodyParam params string
     *
     * @param PassLocationUpdateRequest $request
     * @param PassLocation $passLocation
     * @return Response
     */
    public function update(PassLocationUpdateRequest $request, PassLocation $passLocation)
    {
        $passLocation->update($request->validated());
        return $this->respondCreated(trans('admin/message.pass_location_update'), $passLocation);
    }

    /**
     * Remove pass location.
     *
     * @param PassLocation $passLocation
     * @return Response
     * @throws \Exception
     */
    public function destroy(PassLocation $passLocation)
    {
        $passLocation->delete();
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.pass_location_delete')
        ]);
    }
}
