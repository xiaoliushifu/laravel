<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
		//模拟浏览器，访问项目根目录，
        $this->visit('/')
             ->see('laravel 5');//see一下，是不是有我们期待的文字
    }
}
