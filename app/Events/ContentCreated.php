<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContentCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Content instance.
     *
     * @var array
     */
    public $data;

    /**
     * Create a new event instance.
     *
     * @param  array  $content
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getEventName(){
        return 'content.created';
    }

    public function getEventSource(){
        return $this->data['source'];
    }

    public function getEventContent(){
        return $this->data['content'];
    }
}
