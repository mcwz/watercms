<?php

namespace App\Model;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    use ModelTree, AdminBuilder;
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTitleColumn('name');
    }
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
