<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        for ($i=0; $i < 10; $i++) {
            \App\User::create([
                'name'   => 'name '.$i,
                'email'    => "$i email@qq.com",
                'password' => sha1($i),
            ]);
        }
    }
}
