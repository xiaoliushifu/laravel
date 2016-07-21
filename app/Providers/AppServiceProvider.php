<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *在config/app.php中的providers里的类，绑定时都会调用它的register方法，
     *所以，我们仍然可以在这里再次绑定我们自定义的类（provider)
     *或者，我们写到app.php里的providers数组里，但是写到数组里时，这个自定义的类，
     *得继承，某些方法等等都得有，所以，我们写到这里比较灵活一点。
     * @return void
     */
    public function register()
    {
        //绑定XXX类到容器
        $this->app->bind('A', \App\TA::class);
        $this->app->singleton('B', \App\TB::class);
    }
}
