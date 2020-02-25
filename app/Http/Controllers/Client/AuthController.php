<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 09.01.2019
 * Time: 15:24
 */

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Auth\ClientSocialLoginRequest;
use App\Http\Requests\Client\Auth\LoginRequest;
use App\Http\Requests\Client\Auth\SignUpRequest;
use App\Mail\ClientRegister;
use App\Mail\RegisterVerify;
use App\Models\AreaManager;
use App\Models\Client;
use App\Models\Invite;
use App\Models\User;
use App\Services\Identifier;
use DB;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group Client\auth actions
 */
class AuthController extends Controller
{
    use ThrottlesLogins;

    /**
     * Client registration request.
     *
     * @bodyParam user_id integer required Merchant id.
     * @bodyParam phone string required Client phone.
     * @bodyParam email string required Client email.
     * @bodyParam password string required Client password.
     * @bodyParam first_name string  Client first_name.
     * @bodyParam last_name string  Client last_name.
     * @bodyParam birthday string  Client birthday.
     *
     * @param SignUpRequest $request
     * @return Response
     * @throws \Exception
     */
    public function register(SignUpRequest $request)
    {
        DB::beginTransaction();

        $user = Client::create($request->data());
        $user->loyaltyProgram()->attach($user->user->loyaltyProgram->id, [
            'client_loyalty_id' => \Identifier::generate(Identifier::LOUALTY, $user->id, $user->user->loyaltyProgram->id),
            'point' => $user->user->loyaltyProgram->start_at]);
        $invite = Invite::where('email', $user->email)->where('confirmed', 0)->first();
        if ($invite) {
            $invite->update(['confirmed' => 1]);
        }
        DB::commit();
        Mail::to($user->email)->send(new ClientRegister($user));

        if (!$token = auth('client')->attempt($request->only(['email', 'password']))) {
            return response()->json(['message' => trans('client/message.auth_not_found')], 401);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'user' => auth('client')->user(),
                'token' => $token,
                'message' => trans('client/message.auth_register_success')
            ]
        ]);
    }

    /**
     * Log in request for the client app.
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
        if (!$token = auth('client')->attempt($request->only(['email', 'password']))) {
            return response()->json(['message' => trans('client/message.auth_not_found')], 404);
        }
        if ($request->has('device_type') && $request->input('device_type') != '') {
            auth('client')->user()->update(['device_type' => $request->input('device_type')]);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'user' => auth('client')->user(),
                'token' => $token,
                'marchant_name' => auth('client')->user()->user->full_name,
                'message' => trans('client/message.auth_register_success')
            ]
        ]);
    }

    /**
     * Login iva facebook
     *
     * @param ClientSocialLoginRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function socialLogin(ClientSocialLoginRequest $request)
    {
        DB::beginTransaction();
        if ($user = Client::where('social', $request->input('social'))->first()) {
            $token = auth('client')->login($user);
            $status = 'login';
        } elseif ($user = Client::where('email', $request->input('email'))->first()) {
            $user->update(['social' => $request->input('social')]);
            $token = auth('client')->login($user);
            $status = 'login';
        } else {
            $user = Client::create($request->validated());
            $token = auth('client')->login($user);
            $status = 'register';
            $user->loyaltyProgram()->attach($user->user->loyaltyProgram->id,
                ['client_loyalty_id' => \Identifier::generate(Identifier::LOUALTY, $user->id, $user->user->loyaltyProgram->id),
                    'point' => $user->user->loyaltyProgram->start_at]);
            $invite = Invite::where('email', $user->email)->where('confirmed', 0)->first();
            if ($invite) {
                $invite->update(['confirmed' => 1]);
            }
        }
        if ($request->has('device_type') && $request->input('device_type') != '') {
            $user->update(['device_type' => $request->input('device_type')]);
        }

        DB::commit();
        return response()->json([
            'status' => $status,
            'success' => true,
            'data' => [
                'user' => auth('client')->user(),
                'token' => $token,
                'message' => trans('client/message.auth_register_success')
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
        if (auth()->user()->devices()->exists()) {
            auth()->user()->devices()->delete();
        }
        JWTAuth::invalidate(JWTAuth::getToken());
        DB::commit();

        return response()->json(['message' => 'Successfully logged out']);
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
     * Set client device token
     *
     * @param Request $request
     * @return ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function setDevice(Request $request)
    {
        return response([
            'entity' => auth()->user()->devices()->updateOrCreate([
                'devicable_type' => 'App\Models\Client',
                'devicable_id' => auth()->id()
            ], ['token' => $request->input('token')])
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function merchant()
    {
        return response()->json([
            'entity' => auth()->user()->user
        ]);
    }


    public function checkClient($id)
    {
        if (Client::where('id', $id)->exists()) {
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
