<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsAndRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		//权限表
         Schema::create('permissions', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('label');
			$table->string('description')->nullable();
			$table->timestamps();
		});

		//角色表
		Schema::create('roles', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->string('label');
        $table->string('description')->nullable();
        $table->timestamps();
    });

	//权限-角色关联表
	Schema::create('permission_role', function (Blueprint $table) {
        $table->integer('permission_id')->unsigned();
        $table->integer('role_id')->unsigned();
 
		//外键关联，permission_id字段关联permissions表的id字段，级联删除
        $table->foreign('permission_id')
              ->references('id')
              ->on('permissions')
              ->onDelete('cascade');
	//外键关联，role_id字段关联roles表的id字段
        $table->foreign('role_id')
              ->references('id')
              ->on('roles')
              ->onDelete('cascade');
 
		//复合主键
        $table->primary(['permission_id', 'role_id']);
    });

	/*
	因为表字段users_id的显示长度不一致，导致外键定义出错
	//角色-用户关联表*/
	Schema::create('role_user', function (Blueprint $table) {
        $table->integer('user_id')->unsigned();
        $table->integer('role_id')->unsigned();
 
        $table->foreign('role_id')
              ->references('id')
              ->on('roles')
              ->onDelete('cascade');
		//user_id字段，关联users_login表的users_id字段
        $table->foreign('user_id')
              ->references('users_id')
              ->on('users_login')
              ->onDelete('cascade');
 
        $table->primary(['role_id', 'user_id']);
    });

}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('roles');
		Schema::drop('permissions');
		Schema::drop('permission_role');
		Schema::drop('role_user');
    }
}
