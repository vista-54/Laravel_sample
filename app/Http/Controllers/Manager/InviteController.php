<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 10.07.2019
 * Time: 14:39
 */

namespace App\Http\Controllers\Manager;


use App\Http\Controllers\ApiController;
use App\Http\Requests\Manager\InviteRequest;
use App\Mail\InviteMail;
use CodeItNow\BarcodeBundle\Utils\QrCode;
use Illuminate\Http\JsonResponse;
use Mail;

/**
 * @group Manager\invite actions
 */
class InviteController extends ApiController
{
    /**
     * Display managers invites
     *
     * @return JsonResponse
     */
    public function index()
    {
        return $this->respond([
            'entity' => auth()->user()->invites
        ]);
    }

    /**
     * Create invite to client with email or qr code
     *
     * @bodyParam email string required
     * @bodyParam shop_id integer required
     * @bodyParam type integer required
     *
     * @param InviteRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(InviteRequest $request)
    {
        auth()->user()->invites()->create($request->validated());

        switch ($request->input('type')) {
            case 'email':
                Mail::to($request->input('email'))->send(new InviteMail());
                return $this->respondCreated( trans('manager/message.invite_email'));
            case 'qr':
                $qrCode = new QrCode();
                $qrCode->setText('https://nextcard.app/download')
                    ->setSize(300)
                    ->setPadding(10)
                    ->setErrorCorrection('high')
                    ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
                    ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
                    ->setLabel('Scan Qr Code')
                    ->setLabelFontSize(16)
                    ->setImageType(QrCode::IMAGE_TYPE_PNG);
                return $this->respondCreated(trans('manager/message.invite_qr'), [
                    'qr' => 'data:' . $qrCode->getContentType() . ';base64,' . $qrCode->generate()
                ]);
            case 'whatsapp':

        }

        if ($request->input('type') === 'email') {

        } else {
            if ($request->input('type') === 'qr') {

            } else {
                return $this->respond([
                    'status' => false
                ]);
            }
        }

    }
}
