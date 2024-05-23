<?php

namespace Src\V1\Post\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class PostSubscriptionNotification extends Notification
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var \Src\V1\Post\Http\Resources\PostResource
     */
    protected $data;

    /**
     * @param string $message
     * @param \Src\V1\Post\Http\Resources\PostResource $data
     * @return void
     */
    public function __construct($message, $data)
    {
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * @param \App\Models\User $userto
     * @return array
     */
    public function via($userto)
    {
        return [ "broadcast", "database", ];
    }

    /**
     * @param \App\Models\User $userto
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
     */
    public function toBroadcast($userto)
    {
        return new BroadcastMessage(
        [
            "data" => $this->data,
            "message" => $this->message,
        ]);
    }

    /**
     * @param \App\Models\User $userto
     * @return \Illuminate\Notifications\Messages\BroadcastMessage
     */
    public function toDatabase($userto)
    {
        return new DatabaseMessage(
        [
            "data" => $this->data,
            "message" => $this->message,
        ]);
    }
};
