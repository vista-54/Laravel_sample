<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\PosTerminal\PosTerminalStoreRequest;
use App\Http\Requests\Admin\PosTerminal\PosTerminalUpdateRequest;
use App\Models\PosTerminal;
use Illuminate\Http\Request;

class POSController extends ApiController
{
    public function index(Request $request)
    {
        return $this->respond([
            'entity' => auth()->user()->terminals()->paginate($request->input('limit', 20))
        ]);
    }

    public function store(PosTerminalStoreRequest $request)
    {
        $terminal = auth()->user()->terminals()->create($request->validated());
        if (!$token = auth('api')->attempt($request->validated())) {
            return response()->json(['message' => 'Token error'], 400);
        }
        $terminal->update(['token' => $token]);
        return $this->respondCreated('Terminal added');
    }

    public function update(PosTerminal $posTerminal, PosTerminalUpdateRequest $request)
    {
//        $terminal = $posTerminal->update($request->validated());
    }
}
