<?php

namespace Qz\Models;

use Qz\Scopes\CustomerIdScope;

class AdminRoleGroup extends Model
{
    protected $fillable = [
        'name',
        'customer_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CustomerIdScope());
    }

    public function adminRoles()
    {
        return $this->hasMany(AdminRole::class);
    }
}
