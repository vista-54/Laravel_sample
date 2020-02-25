<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Admin\PassTemplate\PassTemplateStoreRequest;
use App\Http\Requests\Admin\PassTemplate\PassTemplateUpdateRequest;
use App\Models\Pass;
use App\Models\PassTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @group Admin\coupon-template actions
 */
class PassTemplateController extends ApiController
{

    /**
     * Display pass template by pass_id
     *
     * @param Pass $pass
     * @return JsonResponse
     */
    public function getTemplate(Pass $pass)
    {
        return $this->respond([
            'entity' => $pass->passTemplate
        ]);
    }

    /**
     * Store a newly created pass template.
     *
     * @param PassTemplateStoreRequest $request
     * @param PassTemplate $passTemplate
     * @return Response
     */
    public function store(PassTemplateStoreRequest $request, PassTemplate $passTemplate)
    {
        return $this->respondCreated(trans('pass_template_create'), $passTemplate->create($request->validated()));
    }

    /**
     * Display the specified pass template.
     *
     * @param PassTemplate $passTemplate
     * @return Response
     */
    public function show(PassTemplate $passTemplate)
    {
        return $this->respond([
            'entity' => $passTemplate
        ]);
    }

    /**
     * Update pass template.
     *
     * @bodyParam background_color string
     * @bodyParam background_main_color string
     * @bodyParam foreground_color string
     * @bodyParam label_color string
     * @bodyParam points_head string
     * @bodyParam points_value string
     * @bodyParam offer_head string
     * @bodyParam offer_value string
     * @bodyParam customer_head string
     * @bodyParam customer_value string
     * @bodyParam flip_head string
     * @bodyParam flip_value string
     * @bodyParam back_side_head string
     * @bodyParam back_side_value string
     * @bodyParam icon string
     * @bodyParam background_image string
     * @bodyParam stripe_image string
     * @bodyParam customer_id integer
     * @bodyParam unlimited string
     *
     * @param PassTemplateUpdateRequest $request
     * @param PassTemplate $passTemplate
     * @return Response
     */
    public function update(PassTemplateUpdateRequest $request, PassTemplate $passTemplate)
    {
        $passTemplate->touch();
        $passTemplate->update($request->validated());
        return $this->respondCreated(trans('admin/message.pass_template_update'), $passTemplate);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param PassTemplate $passTemplate
     * @return Response
     * @throws \Exception
     */
    public function destroy(PassTemplate $passTemplate)
    {
        $passTemplate->delete();
        return $this->respond([
            'status' => 'success',
            'message' => trans('admin/message.pass_template_delete')
        ]);
    }
}
