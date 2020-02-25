<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 20.06.2019
 * Time: 13:02
 */

namespace App\Services;

use LaravelFCM\Facades\FCM;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\OptionsPriorities;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;

class Notification
{
    /**
     * @param $token
     * @param string $title
     * @param string $body
     * @param array $data
     * @param string $sound
     * @param string $icon
     * @param null $click_action
     * @return mixed
     * @throws \LaravelFCM\Message\Exceptions\InvalidOptionsException
     */
    public function sendNotification($token, $title = 'NextCard', $body = 'Text is empty', $data = ['key' => 'value'], $sound = 'default', $click_action = null, $icon = null)
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);
        $optionBuilder->setPriority(OptionsPriorities::high);
        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body)
            ->setIcon($icon)
            ->setClickAction($click_action)
            ->setSound($sound);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($data);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        return $downstreamResponse->numberSuccess();
    }
}