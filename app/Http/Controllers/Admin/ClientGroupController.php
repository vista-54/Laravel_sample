<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\ClientGroup\ClientGroupStoreRequest;
use App\Http\Requests\Admin\ClientGroup\ClientGroupUpdateRequest;
use App\Models\ClientGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientGroupController extends ApiController
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        return $this->respond([
            'entity' => auth()->user()->clientGroups
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        return $this->respond([
            'entity' => auth()->user()->clientGroups()->paginate($request->input('limit', 20))
        ]);
    }

    /**
     * @param ClientGroup $clientGroup
     * @param ClientGroupUpdateRequest $request
     * @return JsonResponse
     */
    public function update(ClientGroup $clientGroup, ClientGroupUpdateRequest $request)
    {
        \DB::beginTransaction();
        $clientGroup->update(['name' => $request->input('name')]);
        \DB::commit();
        return $this->respondCreated(trans('admin/message.group_update'), $clientGroup);
    }

    /**
     * @param ClientGroup $clientGroup
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(ClientGroup $clientGroup)
    {
        $clientGroup->delete();
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.group_delete')
        ]);
    }
}
