<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\AreaManager\AreaManagerStoreRequest;
use App\Http\Requests\Admin\AreaManager\AreaManagerUpdateRequest;
use App\Models\AreaManager;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Admin\area-manager actions
 */
class AreaManagerController extends ApiController
{
    public function test()
    {
        
    }
    /**
     * Display list of area managers.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->respond([
            'entity' => auth()->user()->areaManagers()->paginate($request->input('limit', 10))
        ]);
    }

    /**
     * Create area manager.
     *
     * @bodyParam name string required
     * @bodyParam email string required
     * @bodyParam password string required
     *
     * @param AreaManagerStoreRequest $request
     * @param AreaManager $areaManager
     * @return Response
     */
    public function store(AreaManagerStoreRequest $request, AreaManager $areaManager)
    {
        return $this->respondCreated(trans('admin/message.manager_store'), $areaManager->create($request->data()));
    }

    /**
     * Display area manger data.
     *
     * @param AreaManager $areaManager
     * @return Response
     */
    public function show(AreaManager $areaManager)
    {
        return $this->respond([
            'entity' => $areaManager
        ]);
    }

    /**
     * Update area manager.
     *
     * @param AreaManagerUpdateRequest $request
     * @param AreaManager $areaManager
     * @return Response
     */
    public function update(AreaManagerUpdateRequest $request, AreaManager $areaManager)
    {
        $areaManager->touch();
        $areaManager->shops()->sync(request()->input('ids'));
        return $this->respondCreated(trans('admin/message.manager_update'), $areaManager->update($request->validated()));
    }

    /**
     * Remove area manager.
     *
     * @param AreaManager $areaManager
     * @return Response
     * @throws \Exception
     */
    public function destroy(AreaManager $areaManager)
    {
        $areaManager->delete();
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.manager_delete')
            ]);
    }
}
