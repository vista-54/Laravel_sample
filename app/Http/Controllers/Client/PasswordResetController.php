<?php

namespace App\Http\Controllers\Client;


use App\Http\Controllers\Controller;
use App\Http\Requests\Client\PasswordReset\CodeRequest;
use App\Http\Requests\Client\PasswordReset\PasswordRequest;
use App\Http\Requests\Client\PasswordReset\ResetPasswordRequest;
use App\Mail\PasswordResetMailApp;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Hash;
use Mail;

/**
 * @group Client\reset pass actions
 */
class PasswordResetController extends Controller
{

    /**
     * Send mail for change pass
     *
     * @bodyParam email string required
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse|int
     */
    public function sendMail(ResetPasswordRequest $request)
    {
        $user = Client::where('email', $request->input('email'))->first();
        if ($user) {
            $code = rand(100000, 999999);
            $user->update(['code' => $code]);
            Mail::to($user->email)->send(new PasswordResetMailApp($user, $code));
            return response()->json(['success' => 'success', 'message' => trans('client/message.reset_code')]);
        } else {
            return response()->json(['success' => false, 'error' => trans('client/message.not_found')]);
        }
    }

    /**
     * Validate confirm code from email
     *
     * @param CodeRequest $request
     * @return JsonResponse
     */
    public function validateCode(CodeRequest $request)
    {
        $user = Client::where('code', $request->input('code'))->first();
        if ($user) {
            return response()->json(['success' => 'success']);
        } else {
            return response()->json(['success' => false, 'message' => trans('admin/message.bad_code')]);
        }
    }

    /**
     * Reset password
     *
     * @bodyParam password string required
     * @bodyParam code string required
     *
     * @param PasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(PasswordRequest $request)
    {
        $user = Client::where('code', $request->input('code'))->first();
        if ($user) {
            $password = Hash::make($request->input('password'));
            $user->update(['code' => null, 'password' => $password]);
            return response()->json(['success' => 'success']);
        } else {
            return response()->json(['success' => false, 'message' =>  trans('admin/message.bad_code')]);
        }
    }
}
