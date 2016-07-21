<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    //定义多对多  一个权限可以被多个角色拥有,一个角色也可以拥有多个权限
			//表示Permission
			public function roles()
			{
			    //第一个参数是关联模型的类名称
			    //Eloquent 假设对应的关联模型数据库表里，外键名称是基于模型名称。
			    //在这个例子里，默认 Role 模型数据库表会以 permission_id 作为外键
				return $this->belongsToMany(Role::class);
				//return $this->hasOne('App\Phone', 'foreign_key', 'local_key');
			}
 
}
