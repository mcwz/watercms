<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;

class Group extends Model
{
    use ModelTree, AdminBuilder;

    public function parent()
    {
        return $this->belongsTo(Group::class, 'parent_id');
    }

    public function users() {
        return $this->belongsToMany(Administrator::class,'user_groups','group_id','user_id');
    }
}
