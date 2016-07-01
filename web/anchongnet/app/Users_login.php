<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Redirect;
/*
*   该模型是操作用户登录表的模块
*/
class Users_login extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'anchong_users_login';
    //主键信息，并没有从数据库中获取，而是根据$primaryKey来判断的，估计是这样的，所以实际表中到底哪个字段是主键，无所谓
    protected $primaryKey ='users_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public  $timestamps=false;

    /*
    *   该方法是添加登录用户账号密码的方法，在注册成功之后会返回用户Token和用户ID还有用户权限
    */
    public function add($user_data)
    {
        //将数据存入登录表
        $this->fill($user_data);
        if($this->save()){
            return true;
        }else{
            return false;
        }
    }

    /*
    *   登陆时的显示用户的token和用户id
    */
    public function quer($field, $quer_data)
    {
        return $this->select($field)->where($quer_data)->get();
    }

    /*
    *   登陆是更新token
    */
    public function addToken($user_data, $users_id){
        $user=$this->where('users_id', '=', $users_id);
        if($user->update($user_data)){
            return true;
        }else{
            return false;
        }

    }

    /*
    *   获得用户token
    */
    public function querToken($guid){
        return $this->select('token')->where('users_id',$guid)->get()->toArray();
    }

    /*
    *   该方法是更新密码
    */
    public function updatepassword($phone,$data)
    {
        $id=$this->where('username','=',$phone);
        if($id->update($data)){
            return true;
        }else{
            return false;
        }
    }

    //通过用户id获取记录
    public function scopeUid($query,$keyUid)
    {
        return $query->where('users_id', '=', $keyUid)->first();
    }
    
    /****************************************************************/
    /*下面四个是定义权限应用时的方法*/
    /****************************************************************/
    
    //定义和Role表的关联，一对多的关联
    public function roles()
    {
        //注释掉其他参数而只保留第一个参数，就会明白laravel底层是如何组织SQL语句的
        //注意，谁是外键，这个字段是外部表的一个主键字段。
        //第三个参数，默认是主表名_id，即是Users_logins_id，该字段应该在role_user表中
        return $this->belongsToMany('App\Role','anchong_role_user','user_id');
    }
    
    //判断是否是某个角色
    public function hasRole($role)
    {
        if(is_string($role)) {
            return $this->roles->contains('name',$role);
        }
        return !! $role->intersect($this->roles)->count();
    }
    
    //是否有某权限
    public function hasPermission($permission)
    {//$permission->roles会调用__get()魔术方法
        return $this->hasRole($permission->roles);
    }
    
    //给用户分配角色
    public function assignRole($role)
    {
        return $this->roles()->save(
            Role::whereName($role)->firstOrFail()
            );
    }
    
}
