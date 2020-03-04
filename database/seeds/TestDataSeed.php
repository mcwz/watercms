<?php

use Illuminate\Database\Seeder;

class TestDataSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Model\Group::insert([
            [
                'parent_id'=>0,
                'order'=>1,
                'title'=>'商务部门',
            ],
            [
                'parent_id'=>0,
                'order'=>2,
                'title'=>'人力资源部门',
            ]
        ]);

        \Encore\Admin\Auth\Database\Role::insert([
            [
                'name' => '编辑',
                'slug' => 'editor',
            ],
            [
                'name' => '审核员',
                'slug' => 'checker',
            ]
        ]);

        \App\Model\Administrator::insert([
            [
                'username' => 'test1',
                'password' => bcrypt('admin'),
                'name'     => '测试员1',
            ],
            [
                'username' => 'test2',
                'password' => bcrypt('admin'),
                'name'     => '测试员2',
            ],
            [
                'username' => 'test3',
                'password' => bcrypt('admin'),
                'name'     => '测试员3',
            ]
        ]);

        \App\Model\UserGroup::insert([
            [
                'user_id'=>2,
                'group_id'=>1,
            ],
            [
                'user_id'=>3,
                'group_id'=>1,
            ],
        ]);
    }
}
