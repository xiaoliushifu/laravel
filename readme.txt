准备创建权限表和角色表
php artisan make:migration create_permissions_and_roles --create=permissions

修改一些表结构及字段对应  涉及到五个表
权限表
角色表
角色权限表
角色用户表
用户表
public function up()
{
    Schema::create('roles', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->string('label');
        $table->string('description')->nullable();
        $table->timestamps();
    });
 
    Schema::create('permissions', function (Blueprint $table) {
        $table->increments('id');
        $table->string('name');
        $table->string('label');
        $table->string('description')->nullable();
        $table->timestamps();
    });
 
    Schema::create('permission_role', function (Blueprint $table) {
        $table->integer('permission_id')->unsigned();
        $table->integer('role_id')->unsigned();
 
        $table->foreign('permission_id')
              ->references('id')
              ->on('permissions')
              ->onDelete('cascade');
 
        $table->foreign('role_id')
              ->references('id')
              ->on('roles')
              ->onDelete('cascade');
 
        $table->primary(['permission_id', 'role_id']);
    });
 
    Schema::create('role_user', function (Blueprint $table) {
        $table->integer('user_id')->unsigned();
        $table->integer('role_id')->unsigned();
 
        $table->foreign('role_id')
              ->references('id')
              ->on('roles')
              ->onDelete('cascade');
 
        $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
 
        $table->primary(['role_id', 'user_id']);
    });
}
 
/*
	//先修改 anchong_users_login的users_id字段为唯一索引，unique key(users_id),而且unsigned
alter table anchong_users_login modify users_id int unsigned  unique key comment '用户id';

	CREATE TABLE `anchong_role_user` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  primary key (`role_id`,`user_id`),
  KEY `role_user_role_id_foreign` (`role_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `anchong_roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_users_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `anchong_users_login` (`users_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
*/



public function down()
{
    Schema::drop('roles');
    Schema::drop('permissions');
    Schema::drop('permission_role');
    Schema::drop('role_user');
}

创建两个模型（user表应该已经创建了吧）
php artisan make:model Permission
php artisan make:model Role

模型表里的代码
	// Permissions.php
public function roles()
{
    return $this->belongsToMany(Role::class);
}
	//Role.php
		public function permissions()
		{
			return $this->belongsToMany(Permission::class);
		}
		public function givePermissionTo($permission)
		{
			return $this->permissions()->save($permission);
		}

	//User.php
		public function roles()
		{
		    return $this->belongsToMany(Role::class);
		}
		// 判断用户是否具有某个角色
		public function hasRole($role)
		{
		    if (is_string($role)) {
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
		}   

//定义权限AuthProviders
$permissions = Permission::with('roles')->get();
        foreach ($permissions as $permission) {
            $gate->define($permission->name, function($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }

// 在代码中，需要进行权限判断的地方，加入$this->authorize(权限名)即可。



php artisan tinker
// 改变命名空间位置，避免下面每次都要输入 App
namespace App
 
// 创建权限
$permission_edit = new Permission
 
$permission_edit->name = 'edit-post'
 
$permission_edit->label = 'Can edit post'
 
$permission_edit->save()
	//创建权限2 
$permission_delete = new Permission
 
$permission_delete->name = 'delete-post'
 
$permission_delete->label = 'Can delete post'
 
$permission_delete->save()

	//创建权限3
$permission_upload = new Permission
 
$permission_upload->name = 'upload'
 
$permission_upload->label = 'Can upload'
 
$permission_upload->save()
 
// 创建角色
$role_editor = new Role
 
$role_editor->name = 'editor';
 
$role_editor->label = 'The editor of the site';
 
$role_editor->save()
 //给role_editor角色分配$permission_edit权限
$role_editor->givePermissionTo($permission_edit)
 
$role_admin = new Role
 
$role_admin->name = 'admin';
 
$role_admin->label = 'The admin of the site';
 
$role_admin->save()
 
// 给$role_admin角色分配edit和delete权限
$role_admin->givePermissionTo($permission_edit)
$role_admin->givePermissionTo($permission_delete)
 
// 创建用户
//$editor = factory(User::class)->create(array('users_name'=>'aaa','users_email'=>'bbb@ccfc'));
$editor = factory(User::class)->create();
$editor = factory(Users_login::class)->create();

$editor = Users_login::find(42);


//PHP error:  Undefined offset: 0 in D:\wamp\www\laravel\vendor\laravel\framework\
//src\Illuminate\Foundation\helpers.php on line 366
 
// 给用户分配角色
$editor->assignRole($role_editor->name)
 
$admin = factory(User::class)->create()
 
$admin->assignRole($role_admin->name)







注意，主键的配置，user_id=7，它把users_login表的id字段值取出了，并没有按照外键关联的users_id的值，
通过看代码可知，它是通过获得主键，才获得了7，而主键在laravel里，Model类里默认都是id，所以，
一直是7，而不是users_id,
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'anchong_role_user.dd' in 'field list'
(SQL: select `anchong_roles`.*, `anchong_role_user`.`user_id` as `pivot_user_id`, `anchong_role_user`.`dd` as `pivot_dd` 
from `anchong_roles` inner join `anchong_role_user` on `anchong_roles`.`id` = `anchong_role_user`.`dd`
where `anchong_role_user`.`user_id` = 7)

alter table anchong_users_login modify `id` int(11) NOT NULL AUTO_INCREMENT unique key COMMENT 'id',
alter table anchong_users_login drop index users_id;
alter table anchong_users_login modify users_id int unsigned  primary key comment '用户users_id';


两个知识点    laravel多表关联的处理    外键关联时，关系的定义。
最后的重点  laravel多表关联。