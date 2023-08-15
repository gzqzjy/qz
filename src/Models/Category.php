<?php

namespace Qz\Models;

use Qz\Scopes\CustomerIdScope;

class Category extends Model
{
    protected $connection = 'common';

    protected $fillable = [
        'customer_id',
        'name',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CustomerIdScope());
    }

    public function adminCategoryDepartments()
    {
        return $this->hasMany(AdminCategoryDepartment::class);
    }
}
