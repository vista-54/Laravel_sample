<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\ContactsTerm\ContactsTermStoreRequest;
use App\Http\Requests\Admin\ContactsTerm\ContactsTermUpdateRequest;
use App\Models\ContactsTerm;
use Illuminate\Http\JsonResponse;

/**
 * @group Admin\terms actions
 */
class ContactsTermController extends ApiController
{

    /**
     * Get loyalty program terms
     *
     * @return JsonResponse
     */
    public function index()
    {
        return $this->respond([
            'entity' => auth()->user()->loyaltyProgram()->first()->contactsTerm
        ]);
    }


    /**
     * Create new term
     *
     * @bodyParam loyalty_program_id integer required
     * @bodyParam company_name string required
     * @bodyParam address string required
     * @bodyParam website string required
     * @bodyParam email string required
     * @bodyParam phone string required
     * @bodyParam conditions string
     *
     * @param ContactsTerm $term
     * @param ContactsTermStoreRequest $request
     * @return JsonResponse
     */
    public function store(ContactsTerm $term, ContactsTermStoreRequest $request)
    {
        return $this->respondCreated(trans('admin/message.contact_term_create'), $term->create($request->all()));
    }

    /**
     * Update terms of loyalty program.
     *
     * @param ContactsTerm $term
     * @param ContactsTermUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(ContactsTerm $term, ContactsTermUpdateRequest $request)
    {
        $term->update($request->all());
        return $this->respondCreated(trans('admin/message.contact_term_update'), $term);
    }
}
