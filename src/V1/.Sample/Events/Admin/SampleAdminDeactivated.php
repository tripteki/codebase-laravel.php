<?php

namespace Src\V1\Sample\Events\Admin;

use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow as IWebSocket;
use Illuminate\Broadcasting\InteractsWithSockets as WebSocketTrait;
use Illuminate\Queue\SerializesModels as SerializationTrait;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;

class SampleAdminDeactivated implements IWebSocket
{
    use SerializationTrait, WebSocketTrait;

    /**
     * @var \Src\V1\Sample\Http\Resources\SampleResource
     */
    public $data;

    /**
     * @param \Src\V1\Sample\Http\Resources\SampleResource $data
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
        return new PrivateChannel("v1.samples.".$this->data->id);
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return "deactivated";
    }
};
