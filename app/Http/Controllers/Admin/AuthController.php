<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 09.01.2019
 * Time: 15:24
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Requests\Admin\Auth\SignUpRequest;
use App\Mail\RegisterVerify;
use App\Models\Card;
use App\Models\LoyaltyProgram;
use App\Models\User;
use App\Models\UserVerification;
use DB;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group Admin\Auth actions
 */
class AuthController extends Controller
{
    use ThrottlesLogins;

    /**
     * Registration request.
     *
     * @bodyParam department string Users department.
     * @bodyParam address string Users surname.
     * @bodyParam email string required Users surname.
     * @bodyParam password string required Users password.
     *
     * @response
     * {
     * "success": true,
     * "message": "Please check your email to confirm your account"
     * }
     *
     * @param SignUpRequest $request
     * @return \Illuminate\Http\Response
     */
    public function register(SignUpRequest $request)
    {
        $user = User::create($request->data());

        $verification_code = rand(100000, 999999); //Generate verification code
        $user->userVerification()->create(['token' => $verification_code]);
//        DB::table('user_verifications')->insert(['user_id' => $user->id, 'token' => $verification_code]);
        Mail::to($user->email)->send(new RegisterVerify($user->department, $verification_code));

        return response()->json([
            'success' => true,
            'message' => trans('admin/message.auth_register')
        ]);
    }

    /**
     *Log in request for the admin panel.
     *
     * @bodyParam email string required Users surname.
     * @bodyParam password string required Users password.
     *
     * @response {
     * "success": true,
     * "data": {
     * "user": {
     * "id": 1,
     * "name": "TestName",
     * "surname": "TestSurname",
     * "email": "Test@Email.com",
     * "role": 2,
     * "role_name": "Client",
     * "sex": null,
     * "country": null,
     * "city": null,
     * "birthday": null,
     * "phone": null,
     * "avatar": null,
     * "courses": []
     * },
     * "token": "Test Bearer Token"
     * }
     * }
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        if (!$token = auth('api')->attempt($request->validated())) {
            return response()->json(['message' => 'We cant find an account with this credentials.'], 401);
        }

        /** @var User $user */
        $user = auth()->user();
        if ($user->verified == 0) {
            return response()->json(['success' => false, 'errors' => ['message' => ['You did not verify your email']]], 500);
        } else {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'currency' => $user->loyaltyProgram->currency
                ]
            ]);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @response {
     * "message": "Successfully logged out"
     * }
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function logout()
    {
        DB::beginTransaction();
//        auth()->user()->devices()->delete();
        JWTAuth::invalidate(JWTAuth::getToken());
        DB::commit();

        return response()->json(['message' => trans('admin/message.auth_logout')]);
    }

//    /**
//     * Refresh a token.
//     *
//     * @return JsonResponse
//     */
//    public function refresh()
//    {
//        return $this->respondWithToken(JWTAuth::refresh());
//    }
//
//    /**
//     * Get the token array structure.
//     *
//     * @param  string $token
//     *
//     * @return JsonResponse
//     */
//    protected function respondWithToken($token)
//    {
//        return response()->json([
//            'access_token' => $token,
//            'token_type' => 'bearer',
//            'expires_in' => JWTAuth::factory()->getTTL() * 60
//        ]);
//    }

    /**
     * Activate users account after email verification.
     *
     * @queryParam verification_code
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function verifyUser($verification_code)
    {
        $check = UserVerification::where('token', $verification_code)->where('verifiable_type', 'App\Models\User')->first();
        if (!is_null($check)) {
            $user = User::find($check->verifiable_id);
            if ($user->verified == 1) {
                return response()->json([
                    'success' => true,
                    'message' =>  trans('admin/message.auth_verified'),
                ]);
            }
            $user->update(['verified' => 1]);
            DB::table('user_verifications')->where('token', $verification_code)->delete();
        }
        return redirect()->to(config('app.url'));
    }
}
