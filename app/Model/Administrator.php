<?php

namespace App\Model;

use Encore\Admin\Traits\AdminBuilder;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

/**
 * Class Administrator.
 *
 * @property Role[] $roles
 */
class Administrator extends \Encore\Admin\Auth\Database\Administrator
{
    /**
     * A user has and groups to many roles.
     *
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        $pivotTable = config('admin.database.user_group_table');

        $relatedModel = config('admin.database.group_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'group_id');
    }
}
