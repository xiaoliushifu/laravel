<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //  多对多
		public function permissions()
		{
			return $this->belongsToMany(Permission::class);
		}

		//给角色添加权限
		public function givePermissionTo($permission)
		{
			return $this->permissions()->save($permission);
		}
 
}
