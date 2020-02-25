<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 09.01.2019
 * Time: 15:24
 */

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Http\Requests\POS\Auth\LoginRequest;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use DB;

/**
 * @group POS\Auth actions
 */
class AuthController extends Controller
{
    use ThrottlesLogins;

    /**
     * Log in request for the POS terminzl.
     *
     * @bodyParam email string required Users surname.
     * @bodyParam password string required Users password.
     *
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        if (!$token = auth('pos')->attempt($request->validated())) {
            return response()->json(['message' => 'Not found'], 401);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'user' => auth('pos')->user(),
                'currency' => auth('pos')->user()->user->loyaltyProgram()->first()->currency,
                'token' => $token,
            ]
        ]);
    }


    /**
     * Log the terminal out (Invalidate the token).
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function logout()
    {
        DB::beginTransaction();
        JWTAuth::invalidate(JWTAuth::getToken());
        DB::commit();

        return response()->json(['message' => 'Logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}
