<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

         //写入类名即可（类的方法run里是实际的SQL逻辑）
         //php artisan db:seed
         //$this->call(UserSeeder::class);

        Model::reguard();
    }
}
