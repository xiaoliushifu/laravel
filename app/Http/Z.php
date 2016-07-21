<?php
namespace App\Http;

//继承Facade
class Z extends \Illuminate\Support\Facades\Facade
{
    
    //固定的protected方法
    protected static  function  getFacadeAccessor()
    {
        return '\App\TA';
    }
}