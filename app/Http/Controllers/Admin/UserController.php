<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\User\ChangePasswordRequest;
use App\Http\Requests\Admin\User\SetAppUrlRequest;
use App\Http\Requests\Admin\User\UserStoreRequest;
use App\Http\Requests\Admin\User\UserUpdateRequest;
use App\Mail\RegisterVerify;
use App\Models\User;
use Base64;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mail;
/**
 * @group Admin\User actions
 */
class UserController extends ApiController
{
    /**
     * Display list of all users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return response()->json([
           'entity' => User::where('role', User::ROLE_MERCHANT)
               ->paginate($request->input('limit', 10))
        ]);
    }

    /**
     * Create new merchant
     *
     * @bodyParam department string
     * @bodyParam email string required
     * @bodyParam password string required
     * @bodyParam business string
     * @bodyParam first_name string
     * @bodyParam last_name string
     * @bodyParam address string
     * @bodyParam timezone string
     * @bodyParam verified integer
     *
     * @param UserStoreRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(UserStoreRequest $request, User $user)
    {
        $user = $user->create($request->data());
        $verification_code = rand(100000, 999999); //Generate verification code
        $user->userVerification()->create(['token' => $verification_code]);
        Mail::to($user->email)->send(new RegisterVerify($user->department, $verification_code));

        return $this->respond([
            'status' => true,
            'message' => trans('admin/message.user_create')
        ]);
    }

    /**
     * Display user data
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user)
    {
        return $this->respond([
            'entity' => $user
        ]);
    }

    /**
     * Update user data
     *
     * @bodyParam department string
     * @bodyParam email string
     * @bodyParam password string
     * @bodyParam business string
     * @bodyParam first_name string
     * @bodyParam last_name string
     * @bodyParam address string
     * @bodyParam timezone string
     * @bodyParam verified integer
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $logo = $request->input('logo');
        if (Str::startsWith($logo, 'data:image')) {
            $user->logo = Base64::save($logo, 'logo', auth()->id() . '/logo');
        }
        if ($logo === null) {
            $user->logo = null;
        }
        $user->update($request->validated());
        return $this->respondCreated(trans('admin/message.user_update'), $user);
    }

    /**
     * Remove user
     *
     * @param User $user
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.user_delete')
        ]);
    }

    /**
     * Change user password
     *
     * @bodyParam password string required
     *
     * @param User $user
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(User $user, ChangePasswordRequest $request)
    {
        return $this->respondCreated(trans('admin/message.user_update_pass'), $user->update($request->data()));
    }


    /**
     * Set users logo
     *
     * @bodyParam logo string required Base64 string
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logo(Request $request)
    {
        $user = auth()-> user();
        $logo = $request->input('logo');
        if (Str::startsWith($logo, 'data:image')) {
            $user->logo = Base64::save($logo, 'logo', auth()->id() . '/logo');
        }
        if ($logo === null) {
            $user->logo = null;
        }
        $user->save();
        return $this->respondCreated(trans('admin/message.logo_update'), $user);
    }

    public function setAppUrl(SetAppUrlRequest $request)
    {
        auth()->user()->update($request->validated());
        return $this->respond([
            'entity' => auth()->user(),
            'message' => 'Url has been set'
        ]);
    }
}
