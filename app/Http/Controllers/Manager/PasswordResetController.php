<?php

namespace App\Http\Controllers\Manager;


use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\PasswordReset\CodeRequest;
use App\Http\Requests\Manager\PasswordReset\PasswordRequest;
use App\Http\Requests\Manager\PasswordReset\ResetPasswordRequest;
use App\Mail\PasswordResetMail;
use App\Models\AreaManager;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Mail;

/**
 * @group Manager\reset pass actions
 */
class PasswordResetController extends Controller
{
    /**
     * @param ResetPasswordRequest $request
     * @return \Illuminate\Http\JsonResponse|int
     */
    public function sendMail(ResetPasswordRequest $request)
    {
        $user = AreaManager::where('email', $request->input('email'))->first();
        if ($user) {
            $code = rand(100000, 999999);
            $user->passwordReset()->create(['code' => $code]);
            Mail::to($user->email)->send(new PasswordResetMail($user->department, $code));
            return response()->json(['success' => 'success', 'message' => trans('admin/message.reset_code')]);
        } else {
            return response()->json(['success' => false, 'message' => trans('admin/message.not_found')]);
        }
    }

    /**
     * @param CodeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateCode(CodeRequest $request)
    {
        $user = AreaManager::whereHas('passwordReset', function ($item) use ($request) {
            /** @var PasswordReset $item */
            $item->where('code', $request->input('code'));
        })->first();
        if ($user) {
            return response()->json(['success' => 'success']);
        } else {
            return response()->json(['success' => false, 'message' => trans('admin/message.bad_code')]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function resetPassword(PasswordRequest $request)
    {
        $user =  AreaManager::whereHas('passwordReset', function ($item) use ($request) {
            /** @var PasswordReset $item */
            $item->where('code', $request->input('code'));
        })->first();
        if ($user) {
            $password = Hash::make($request->input('password'));
            $user->update(['password' => $password]);
            $user->passwordReset()->first()->delete();
            return response()->json(['success' => 'success']);
        } else {
            return response()->json(['success' => false, 'message' => trans('admin/message.bad_code')]);
        }
    }
}
