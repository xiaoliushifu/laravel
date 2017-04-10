<?php

namespace App\Listeners;

use App\Events\SomeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class EventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *监听者的handle方法的参数，就是它所监听的事件的实例
     * @param  SomeEvent  $event
     * @return void
     */
    public function handle(SomeEvent $event)
    {
        //
        return $event->name;
        //阻止事件冒泡，因为$event可能被多个监听者进行监听，多个监听者按照顺序监听并执行
        //return false可以阻止后续的监听者
        //return false;
    }
}
