<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use App\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;
    
   // public $primaryKey='users_id';
   // public $title = 'good';
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';
	

	//public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *fillable 属性指定了哪些字段支持批量赋值 。可以设定在类的属性里或是实例化后设定。
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];
    //protected $fillable = ['users_name', 'users_email', 'users_pwd'];
    //protected $guarded = ['name', 'password'];
    /**
     * guarded 与 fillable 相反，是作为「黑名单」而不是「白名单」：
     * */
    //protected $guarded = ['users_name', 'users_pwd'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

	public function userTest()
	{
		//这个方法在哪里出现了呢，父类Model中没有，难道是接口中？
		//return $this->findOrfail(4);
		return $this->where('name','<>','laoliu')->get();
	}

	public function userAdd()
	{
		$this->name='yanri';
		$email=bin2hex(mt_rand(0,9)).'@'.mt_rand(20,99).'.com';
		$user_data = array('name'=>'user2','email'=>$email);
		$this->fill($user_data);
		$this->save();
	}

	public function userRead()
	{
		return $this->all();
	}

	public function userUpdate()
	{
		$users=$this->where('password','=','4299');
		var_dump($users);die();
		$users->update(array('name'=>'dabai','password'=>mt_rand(2000,9999)));
		//$users->save();
	}


		public function roles()
		{
			return $this->belongsToMany(Role::class);
		}
		// 判断用户是否具有某个角色
		public function hasRole($role)
		{
			if (is_string($role)) {
			    //这里为什么写roles而不是roles()呢？
				return $this->roles->contains('name', $role);
			}
		 
			return !! $role->intersect($this->roles)->count();
		}
		// 判断用户是否具有某权限
		public function hasPermission($permission)
		{
			return $this->hasRole($permission->roles);
		}
		// 给用户分配角色
		public function assignRole($role)
		{
			return $this->roles()->save(
				Role::whereName($role)->firstOrFail()
			);
		}




}
