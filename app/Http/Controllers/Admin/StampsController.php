<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Stamps\StampsUpdateRequest;
use App\Models\Card;
use App\Models\Stamps;
use Illuminate\Http\Request;
/**
 * @group Admin\Stamps actions
 */
class StampsController extends ApiController
{
    /**
     * Show one stamp
     *
     * @param Stamps $stamps
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Stamps $stamps)
    {
        return $this->respond([
            'entity' => $stamps
        ]);
    }

    /**
     * Update loyalty programs stamps
     *
     * @bodyParam stamps_number integer
     * @bodyParam background_color string
     * @bodyParam background_image string
     * @bodyParam stamp_color string
     * @bodyParam unstamp_color string
     * @bodyParam stamp_image string
     * @bodyParam unstamp_image string
     *
     * @param Stamps $stamps
     * @param StampsUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Stamps $stamps, StampsUpdateRequest $request)
    {
        $stamps->touch();
        $stamps->update($request->validated());
        return $this->respondCreated(trans('admin/message.stamps_update'), $stamps);
    }
}
