<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 09.01.2019
 * Time: 15:24
 */

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Requests\Manager\Auth\LoginRequest;
use App\Models\AreaManager;
use App\Models\ClientShop;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use DB;

/**
 * @group Manager\Auth actions
 */
class AuthController extends Controller
{
    use ThrottlesLogins;

    /**
     * Log in request for the manager app.
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
        if (!$token = auth('manager')->attempt($request->validated())) {
            return response()->json(['message' => trans('manager/message.auth_not_found')], 401);
        }
        $shop = ClientShop::where('created_by', auth('manager')->user()->id)->latest()->first();
        return response()->json([
            'success' => true,
            'data' => [
                'user' => auth('manager')->user(),
                'currency' => auth('manager')->user()->user->loyaltyProgram()->first()->currency,
                'token' => $token,
                'last_shop' => $shop ? $shop->shop : null
            ]
        ]);
    }


    /**
     * Log the user out (Invalidate the token).
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

        return response()->json(['message' => trans('manager/message.auth_log_out')]);
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

    /**
     * @param $id
     * @return JsonResponse
     */
    public function checkManager($id)
    {
        if (AreaManager::where('id', $id)->exists()) {
            return response()->json([
                'status' => true
            ]);
        } else {
            return response()->json([
                'status' => false
            ]);
        }
    }
}
