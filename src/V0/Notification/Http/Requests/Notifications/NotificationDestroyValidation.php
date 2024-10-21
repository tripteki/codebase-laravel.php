<?php

namespace Src\V0\Notification\Http\Requests\Notifications;

use Illuminate\Validation\Rule;
use Illuminate\Notifications\DatabaseNotification as NotificationModel;
use Tripteki\Helpers\Contracts\AuthModelContract;
use Tripteki\Helpers\Http\Requests\FormValidation;
use Illuminate\Support\Facades\Auth;

class NotificationDestroyValidation extends FormValidation
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

            "notification" => [

                "sometimes",
                "nullable",
                "string",
                Rule::exists($provider, keyName($provider))->where(function ($query) {

                    return $query->where("notifiable_type", get_class(app(AuthModelContract::class)))->where("notifiable_id", Auth::id());
                }),
            ],
        ];
    }
};
