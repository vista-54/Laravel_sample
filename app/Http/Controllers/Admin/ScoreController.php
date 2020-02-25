<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\Score\ScoreStoreRequest;
use App\Models\Score;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @group Admin\score actions
 */
class ScoreController extends ApiController
{

    /**
     * Get loyalty card score config
     *
     * @return JsonResponse
     */
    public function index()
    {
        return $this->respond([
            'entity' => auth()->user()->loyaltyProgram->score
        ]);
    }

    /**
     * Create score.
     *
     * @bodyParam loyalty_program_id integer required
     * @bodyParam set_email integer
     * @bodyParam set_phone integer
     * @bodyParam set_card integer
     * @bodyParam scan_card integer
     *
     * @param Score $score
     * @param ScoreStoreRequest $request
     * @return Response
     */
    public function store(Score $score, ScoreStoreRequest $request)
    {
        return $this->respondCreated(trans('admin/message.score_create'), $score->create($request->validated()));
    }

    /**
     * Display score.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return $this->respond([
            'entity' => Score::where('loyalty_program_id', $id)->get()
        ]);
    }

    /**
     * Update score.
     *
     * @bodyParam loyalty_program_id integer
     * @bodyParam set_email integer
     * @bodyParam set_phone integer
     * @bodyParam set_card integer
     * @bodyParam scan_card integer
     *
     * @param Score $score
     * @param ScoreStoreRequest $request
     * @return Response
     */
    public function update(Score $score, ScoreStoreRequest $request)
    {
        $score->update($request->validated());
        return $this->respondCreated(trans('admin/message.score_update'), $score);
    }

    /**
     * Remove score
     *
     * @param Score $score
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(Score $score)
    {
        $score->delete();
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.score_delete')
        ]);
    }
}
