<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Post;

class BlogView extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     * 事件仅仅注入了一个实例而已!
     * @return void
     */
    public function __construct(Post $post)
    {
        //
        $this->post = $post;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
