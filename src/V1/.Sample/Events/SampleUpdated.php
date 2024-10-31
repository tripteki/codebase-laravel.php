<?php

namespace Src\V1\Sample\Events;

use Src\V1\Common\Helpers\ContentHelper;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow as IWebSocket;
use Illuminate\Broadcasting\InteractsWithSockets as WebSocketTrait;
use Illuminate\Queue\SerializesModels as SerializationTrait;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;

class SampleUpdated implements IWebSocket
{
    use SerializationTrait, WebSocketTrait;

    /**
     * @var \Tripteki\Helpers\Contracts\IResponse
     */
    public $data;

    /**
     * @param \Tripteki\Helpers\Contracts\IResponse $data
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
        return new PrivateChannel("v1.samples.".(new ContentHelper)($this->data)->id);
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return "updated";
    }
};
