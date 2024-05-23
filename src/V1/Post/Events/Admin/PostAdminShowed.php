<?php

namespace Src\V1\Post\Events\Admin;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow as IWebSocket;
use Illuminate\Broadcasting\InteractsWithSockets as WebSocketTrait;
use Illuminate\Queue\SerializesModels as SerializationTrait;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;

class PostAdminShowed implements IWebSocket
{
    use SerializationTrait, WebSocketTrait;

    /**
     * @var \Src\V1\Post\Http\Resources\PostResource
     */
    public $data;

    /**
     * @param \Src\V1\Post\Http\Resources\PostResource $data
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Broadcasting\PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel("v1.users.".$this->data->user->id);
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return "showed";
    }
};
