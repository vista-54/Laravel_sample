<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\LoyaltyProgram\LoyaltyProgramUpdateRequest;
use App\Http\Resources\Admin\LoyaltyProgram\LoyaltyProgramResource;
use App\Models\Client;
use App\Models\Device;
use App\Models\LoyaltyProgram;
use App\Models\User;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

/**
 * @group Admin\loyalty-program actions
 */
class LoyaltyProgramController extends ApiController
{
    /**
     * Return users loyalty program data
     *
     * @return JsonResponse
     */
    public function loyaltyProgram()
    {
        if ($program = LoyaltyProgram::where('user_id', auth()->user()->id)->first()) {
            return $this->respond([
                'entity' => new LoyaltyProgramResource($program)
            ]);
        } else {
         throw new ModelNotFoundException(trans('admin/message.loyalty_program_not_found'));
        }
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function loyaltyProgramByUser(User $user)
    {
        return $this->respond([
            'entity' => new LoyaltyProgramResource(LoyaltyProgram::where('user_id', $user->id)->first())
        ]);
    }

    /**
     * @param LoyaltyProgram $program
     * @return JsonResponse
     */
    public function settings(LoyaltyProgram $program)
    {
        return $this->respond([
            'entity' => [
                'program' => $program,
                'terms' => $program->contactsTerm,
                'score' => $program->score
            ]
        ]);
    }

    /**
     * Update loyalty program settings
     *
     * @bodyParam user_id string
     * @bodyParam title string
     * @bodyParam description string
     * @bodyParam country string
     * @bodyParam language string
     * @bodyParam currency string
     * @bodyParam currency_value string
     * @bodyParam link string
     * @bodyParam start_at integer
     * @bodyParam company_name string
     * @bodyParam address string
     * @bodyParam website string
     * @bodyParam email string
     * @bodyParam phone string
     * @bodyParam conditions string
     * @bodyParam set_email integer
     * @bodyParam set_phone integer
     * @bodyParam set_card integer
     * @bodyParam scan_card integer
     *
     * @param LoyaltyProgram $loyaltyProgram
     * @param LoyaltyProgramUpdateRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(LoyaltyProgram $loyaltyProgram, LoyaltyProgramUpdateRequest $request)
    {
        DB::beginTransaction();
        $loyaltyProgram->update($request->validated());
        $loyaltyProgram->contactsTerm()->first()->update($request->validated());
        $loyaltyProgram->score()->first()->update($request->validated());
        DB::commit();
//        auth()->user()->clients->map(function ($item) use ($loyaltyProgram) {
//            /** @var Client $item */
//            $item->devices->map(function ($item) use ($loyaltyProgram) {
//                /** @var Device $item */
//                \Notify::sendNotification($item->token, 'NextCard', 'Loyalty program updated', ['entity' => $loyaltyProgram, 'actions' => 'card_update']);
//            });
//        });
        return $this->respondCreated(trans('admin/message.loyalty_program_update'));
    }
}
