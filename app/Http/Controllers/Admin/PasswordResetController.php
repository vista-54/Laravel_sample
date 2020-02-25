<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PasswordReset\CodeRequest;
use App\Http\Requests\Admin\PasswordReset\PasswordRequest;
use App\Http\Requests\Admin\PasswordReset\ResetPasswordRequest;
use App\Mail\PasswordResetMail;
use App\Models\PasswordReset;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Hash;
use Mail;
/**
 * @group Admin\reset pass actions
 */
class PasswordResetController extends Controller
{
    /**
     * @param ResetPasswordRequest $request
     * @return JsonResponse|int
     */
    public function sendMail(ResetPasswordRequest $request)
    {
        $user = User::where('email', $request->input('email'))->first();
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
     * @return JsonResponse
     */
    public function validateCode(CodeRequest $request)
    {
        $user = User::whereHas('passwordReset', function ($item) use ($request) {
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
     * @param PasswordRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function resetPassword(PasswordRequest $request)
    {
        $user =  User::whereHas('passwordReset', function ($item) use ($request) {
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
