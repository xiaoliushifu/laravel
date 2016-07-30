<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Permission;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *在这里注册策略类
     *即把User类的权限操作，交给TaskPolicy来处理
     *'App\User'=>'App\Policies\TaskPolicy',
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        //为App\User的Model类注册一个策略类，将来涉及User权限的操作都交给TaskPolicy来处理
        //'App\User'=>'App\Policies\TaskPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        parent::registerPolicies($gate);


         //必须先定义好权限，才能在后续时检测
		/*   $permissions = Permission::with('roles')->get();
		  //dump($permissions);
		  //var_dump($permissions);exit;
        foreach ($permissions as $permission) {
            //define方法，传入第一个参数，权限名（delete-post)，
            //第二个参数，是一个回调函数，该回调函数，可以传递两个参数，第一个参数是用户
            //熟悉一下functoin use语法 $permission在定义时就确定了当前的值。
            $gate->define($permission->name, function($user) use ($permission) {
                //通过hasPermission方法，去数据库中比对
                return $user->hasPermission($permission);
            });
        } */



    }
}
