<?php

namespace Src\V0\Notification\Http\Requests\Admin\Notifications;

use Illuminate\Notifications\DatabaseNotification as NotificationModel;
use Tripteki\Helpers\Http\Requests\FormValidation;

class NotificationShowValidation extends FormValidation
{
    /**
     * @return void
     */
    protected function preValidation()
    {
        return [

            "notification" => $this->route("notification"),
        ];
    }

    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules()
    {
        $provider = NotificationModel::class;

        return [

            "notification" => "required|string|exists:".$provider.",".keyName($provider),
        ];
    }
};
