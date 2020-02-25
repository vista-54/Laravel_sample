<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Pass\PassStoreRequest;
use App\Http\Requests\Admin\Pass\PassUpdateRequest;
use App\Http\Resources\Admin\Pass\PassShowResource;
use App\Models\Client;
use App\Models\ClientPass;
use App\Models\Device;
use App\Models\Pass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DB;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Validator;

/**
 * @group Admin\Coupon actions
 */
class PassController extends ApiController
{

    public function __construct()
    {
        $this->middleware('ownOrAdmin')->only(['show']);
    }

    /**
     * Display all passes.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->respond([
            'entity' => auth()->user()->passes()->paginate($request->input('limit', 10))
        ]);
    }

    /**
     * Create new pass.
     *
     * @bodyParam user_id integer required
     * @bodyParam title string required
     * @bodyParam description string
     *
     * @param Pass $pass
     * @param PassStoreRequest $request
     * @return Response
     */
    public function store(Pass $pass, PassStoreRequest $request)
    {
        return $this->respondCreated(trans('admin/message.pass_create'), $pass->create($request->validated()));
    }

    /**
     * Display one pass.
     *
     * @param Pass $pass
     * @return Response
     */
    public function show(Pass $pass)
    {
        return $this->respond([
            'entity' => new PassShowResource($pass)
        ]);
    }

    /**
     * Update pass.
     *
     * @bodyParam user_id integer
     * @bodyParam title string
     * @bodyParam description string
     *
     * @param Request $request
     * @param Pass $pass
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, Pass $pass)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'string|max:255|nullable',
            'value' => 'integer|min:0',
            'availability_count' => 'integer',
            'start_date' => 'before:end_date|nullable',
            'end_date' => 'nullable',
            'status' => 'boolean',
            'expiration_date' => 'date|nullable',
            'margin_value' => 'numeric|nullable'
        ]);
        if ($validator->fails()) {
            abort(422);
        }
        $pass->update($validator->validated());

        return $this->respondCreated(trans('admin/message.pass_update'), $pass);
    }

    /**
     * Remove pass.
     *
     * @param Pass $pass
     * @return Response
     * @throws \Exception
     */
    public function destroy(Pass $pass)
    {
        $pass->delete();
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.pass_delete')
        ]);
    }

    /**
     * Block or unblock pass
     *
     * @param Pass $pass
     * @return JsonResponse
     */
    public function block(Pass $pass)
    {
        if ($pass->status === 1) {
            $pass->update(['status' => 0]);
            return $this->respondCreated(trans('admin/message.pass_locked'));
        } else {
            $pass->update(['status' => 1]);
            auth()->user()->clients->map(function ($item) use ($pass) {
                /** @var Client $item */
                $item->devices->map(function ($item) use ($pass) {
                    /** @var Device $item */
                    \Notify::sendNotification($item->token, 'NextCard', 'A new coupon is available! ' . $pass->title, ['entity' => $pass, 'actions' => 'cupon_update', 'default', null, 'icon/icon.png']);
                });
            });
            return $this->respondCreated(trans('admin/message.pass_unlocked'));
        }
    }

    /**
     * Set pass to valid or redeemed
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function valided()
    {
        auth()->user()->passes->map(function ($item) {
            /** @var Pass $item */
            $item->clientPasses()->delete();
        });

        return $this->respond([
            'message' => trans('admin/message.pass_valided'),
            'status' => 'success'
        ]);
    }

    public function passList()
    {
        return $this->respond([
            'entity' => auth()->user()->passes()->pluck('title')
        ]);
    }
}
