<?php

use Illuminate\Database\Seeder;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Menu;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Administrator::truncate();
        Administrator::create([
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'name'     => '超级管理员',
        ]);

        // create a role.
        Role::truncate();
        Role::create([
            'name' => '管理员',
            'slug' => 'administrator',
        ]);

        // add role to user.
        Administrator::first()->roles()->save(Role::first());

        //create a permission
        Permission::truncate();
        Permission::insert([
            [
                'name'        => '所有权限',
                'slug'        => '*',
                'http_method' => '',
                'http_path'   => '*',
            ],
            [
                'name'        => '信息面板',
                'slug'        => 'dashboard',
                'http_method' => 'GET',
                'http_path'   => '/',
            ],
            [
                'name'        => '登录',
                'slug'        => 'auth.login',
                'http_method' => '',
                'http_path'   => "/auth/login\r\n/auth/logout",
            ],
            [
                'name'        => '用户设置',
                'slug'        => 'auth.setting',
                'http_method' => 'GET,PUT',
                'http_path'   => '/auth/setting',
            ],
            [
                'name'        => '权限管理',
                'slug'        => 'auth.management',
                'http_method' => '',
                'http_path'   => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs",
            ],
            [
                'name'        => '系统日志',
                'slug'        => 'ext.log-viewer',
                'http_method' => '',
                'http_path'   => '/logs*',
            ],
            [
                'name'        => '系统变量',
                'slug'        => 'ext.config',
                'http_method' => '',
                'http_path'   => "/config*",
            ],
            [
                'name'        => '数据备份',
                'slug'        => 'ext.backup',
                'http_method' => '',
                'http_path'   => "/backup*",
            ],
        ]);

        Role::first()->permissions()->save(Permission::first());

        // add default menus.
        Menu::truncate();
        Menu::insert([
            [
                'parent_id' => 0,
                'order'     => 1,
                'title'     => '信息面版',
                'icon'      => 'fas fa-bar-chart',
                'uri'       => '/',
            ],
            [
                'parent_id' => 0,
                'order'     => 10,
                'title'     => '系统管理',
                'icon'      => 'fas fa-tasks',
                'uri'       => '',
            ],
            [
                'parent_id' => 2,
                'order'     => 3,
                'title'     => '用户管理',
                'icon'      => 'fas fa-users',
                'uri'       => 'auth/users',
            ],
            [
                'parent_id' => 2,
                'order'     => 4,
                'title'     => '角色管理',
                'icon'      => 'fas fa-user',
                'uri'       => 'auth/roles',
            ],
            [
                'parent_id' => 2,
                'order'     => 5,
                'title'     => '用户组管理',
                'icon'      => 'fas fa-users',
                'uri'       => 'groups',
            ],
            [
                'parent_id' => 2,
                'order'     => 6,
                'title'     => '权限管理',
                'icon'      => 'fas fa-ban',
                'uri'       => 'auth/permissions',
            ],
            [
                'parent_id' => 2,
                'order'     => 7,
                'title'     => '菜单管理',
                'icon'      => 'fas fa-bars',
                'uri'       => 'auth/menu',
            ],
            [
                'parent_id' => 2,
                'order'     => 8,
                'title'     => '操作日志',
                'icon'      => 'fas fa-history',
                'uri'       => 'auth/logs',
            ],
            [
                'parent_id' => 2,
                'order'     => 9,
                'title'     => '系统日志',
                'icon'      => 'fa fa-database',
                'uri'       => 'logs',
            ],
            [
                'parent_id' => 2,
                'order'     => 10,
                'title'     => '系统变量',
                'icon'      => 'fas fa-toggle-on',
                'uri'       => 'config',
            ],
            [
                'parent_id' => 2,
                'order'     => 11,
                'title'     => '数据备份',
                'icon'      => 'fas fa-copy',
                'uri'       => 'backup',
            ],
            [
                'parent_id' => 0,
                'order'     => 2,
                'title'     => '内容管理',
                'icon'      => 'fas fa-archive',
                'uri'       => 'categories',
            ],


        ]);

        // add role to menu.
        Menu::find(2)->roles()->save(Role::first());

        \App\Model\Group::insert([
            [
                'parent_id'=>0,
                'order'=>0,
                'title'=>'IT运维部门',
            ]
        ]);

        \App\Model\Category::insert([
            [
                'name'=>env('APP_NAME'),
                'type'=>1,
                'uri'=>env('APP_URL')
            ]
        ]);
    }
}
