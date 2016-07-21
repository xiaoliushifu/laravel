<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

//php artisan make:request StoreBlogPostRequest
/**
 * 
 * @author Administrator
 *可以写固定的方法，比如rules,authentics等，类似于中间件吧。
 */
abstract class Request extends FormRequest
{
    //
}
