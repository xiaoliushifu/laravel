<?php

namespace App\Listeners;

use App\Events\BlogView;
use Illuminate\Queue\InteractsWithQueue;
//use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Session\Store;

class BlogViewListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Store $session)
    {
        ////session实例的底层封装，都是Store类的操作，所以这里就不再使用Session，而是Store
        $this->session=$session;
    }

    public function hasViewedBlogs($post)
    {
        return array_key_exists($post->id,$this->getViewedBlogs());
    }
    
    /**
     * 
     * @param unknown $blog
     */
    
    public function getViewedBlogs()
    {
        return $this->session->get('viewed_blogs',[]);
    }
        
    public function storeViewedBlogs($post)
    {
        $key='viewed_blogs.'.$post->id;
        //put方法会按照'.'拆分为数组，这样就以viewed_blogs的二维数组了
        return $this->session->put($key,time());
    }
    /**
     * Handle the event.
     *
     * @param  BlogView  $event
     * @return void
     */
    public function handle(BlogView $event)
    {
        //
        $post = $event->post;
        if (!$this->hasViewedBlogs($post)) {
            $post->view_cache = $post->view_cache+1;
            $post->save();
            //本次会话保存一次浏览
            $this->storeViewedBlogs($post);
        }
    }
}
