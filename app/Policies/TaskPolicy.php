<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    //为每一个权限编写一个方法，方法名，就是权限名
    //通常，一个策略类方法对应一个控制器上的方法，
    //策略类方式，在好几种验证权限的方式时，必须传递第二个参数。如Gate::check('index',$user);
    public function index()
    {
        echo 'policy called<BR />';
        return true;
    }
    
    
    
}
