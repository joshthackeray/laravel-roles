<?php

namespace JoshThackeray\Roles\Models;

use JoshThackeray\Roles\Traits\Assignable;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use Assignable;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'label', 'description', 'status'];

    /**
     * The attributes that should be visible in serialization.
     *
     * @var array
     */
    protected $visible = ['label', 'description'];

}
