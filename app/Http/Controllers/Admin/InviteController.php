<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Models\AreaManager;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Admin\invite actions
 */
class InviteController extends ApiController
{
    /**
     * Display area manager invite list.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = Invite::whereHas('areaManager', function ($q) {
            /** @var AreaManager $q */
            $q->whereHas('user', function ($q) {
                /** @var User $q */
                $q->where('id', auth()->user()->id);
            });
        })->paginate($request->input('limit'));

         return $this->respondWithPagination($data, $data);
    }

    /**
     * Remove invite.
     *
     * @param Invite $invite
     * @return Response
     * @throws \Exception
     */
    public function destroy(Invite $invite)
    {
        $invite->delete();
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.invite_delete')
        ]);
    }
}
