<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Permission;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        //获取权限，因为已经定义好了Permission和Role模型，它们是多对多关系，所以可以使用Permission->roles()->get()
		$permissions = Permission::with('roles')->get();
		//定义权限
        foreach ($permissions as $permission) {
            $gate->define($permission->name, function($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }
}
