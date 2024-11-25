<?php

namespace Src\V1\Api\User\Notifications;

use Src\V1\Api\User\Events\UserActivatedEvent;
use Src\V1\Api\User\Events\UserDeactivatedEvent;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;

class UserAccountNotification extends Notification
{
    /**
     * @var \Src\V1\Api\User\Events\UserActivatedEvent|\Src\V1\Api\User\Events\UserDeactivatedEvent $userEvent
     */
    protected \Src\V1\Api\User\Events\UserActivatedEvent|\Src\V1\Api\User\Events\UserDeactivatedEvent $userEvent;

    /**
     * @param \Src\V1\Api\User\Events\UserActivatedEvent|\Src\V1\Api\User\Events\UserDeactivatedEvent $userEvent
     * @return void
     */
    public function __construct(UserActivatedEvent|UserDeactivatedEvent $userEvent)
    {
        $this->userEvent = $userEvent;
    }

    /**
     * @param object $eventTo
     * @return array
     */
    public function via(object $eventTo): array
    {
        return [

            "broadcast",
            "database",
            "mail",
        ];
    }

    /**
     * @param object $eventTo
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
     */
    public function toBroadcast(object $eventTo): BroadcastMessage
    {
        return new BroadcastMessage(
        [
            "user" => $this->userEvent,
        ]);
    }

    /**
     * @return string
     */
    public function broadcastType(): string
    {
        return "user.account";
    }

    /**
     * @param object $eventTo
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toDatabase(object $eventTo): DatabaseMessage
    {
        return new DatabaseMessage(
        [
            "user" => $this->userEvent,
        ]);
    }

    /**
     * @return string
     */
    public function databaseType(): string
    {
        return "user.account";
    }

    /**
     * @param object $eventTo
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $eventTo): MailMessage
    {
        if ($this->userEvent instanceof UserActivatedEvent) {
            return (new MailMessage)
                ->greeting(__("module.user.api.account_activated.greeting", [ "name" => $eventTo->name, ]))
                ->subject(__("module.user.api.account_activated.subject"))
                ->line(__("module.user.api.account_activated.line"))
                ->line(__("module.user.api.account_activated.thank_you"));
        }

        if ($this->userEvent instanceof UserDeactivatedEvent) {
            return (new MailMessage)
                ->greeting(__("module.user.api.account_deactivated.greeting", [ "name" => $eventTo->name, ]))
                ->subject(__("module.user.api.account_deactivated.subject"))
                ->line(__("module.user.api.account_deactivated.line"))
                ->line(__("module.user.api.account_deactivated.thank_you"));
        }
    }
}
