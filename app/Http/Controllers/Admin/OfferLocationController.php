<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\OfferLocation\OfferLocationStoreRequest;
use App\Http\Requests\Admin\OfferLocation\OfferLocationUpdateRequest;
use App\Models\Offer;
use App\Models\OfferLocation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Admin\offer-location actions
 */
class OfferLocationController extends ApiController
{
    /**
     * Display a listing of offer locations.
     *
     * @return Response
     */
    public function offerLocations(Offer $offer)
    {
        return $this->respond([
            'entity' => $offer->offerLocations
        ]);
    }

    /**
     * Create offer location.
     *
     * @bodyParam offer_id integer required
     * @bodyParam latitude string required
     * @bodyParam longitude string required
     * @bodyParam params string
     *
     * @param OfferLocationStoreRequest $request
     * @param OfferLocation $offerLocation
     * @return Response
     */
    public function store(OfferLocationStoreRequest $request, OfferLocation $offerLocation)
    {
        return $this->respondCreated(trans('admin/message.offer_location_create'), $offerLocation->create($request->validated()));
    }

    /**
     * Display offer location.
     *
     * @param OfferLocation $offerLocation
     * @return Response
     */
    public function show(OfferLocation $offerLocation)
    {
        return $this->respond([
            'entity' => $offerLocation
        ]);
    }

    /**
     * Update offer location.
     *
     * @bodyParam loyalty_program_id integer
     * @bodyParam latitude string
     * @bodyParam longitude string
     * @bodyParam params string
     *
     * @param OfferLocationUpdateRequest $request
     * @param OfferLocation $offerLocation
     * @return Response
     */
    public function update(OfferLocationUpdateRequest $request, OfferLocation $offerLocation)
    {
        $offerLocation->update($request->validated());
        return $this->respondCreated(trans('admin/message.offer_location_update'), $offerLocation);
    }

    /**
     * Remove offer location.
     *
     * @param OfferLocation $offerLocation
     * @return Response
     * @throws \Exception
     */
    public function destroy(OfferLocation $offerLocation)
    {
        $offerLocation->delete();
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.offer_location_delete')
        ]);
    }
}
