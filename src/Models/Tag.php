<?php

namespace Qz\Models;

class Tag extends Model
{
    protected $connection = 'common';
    protected $fillable = [
        'name',
        'status',
        'customer_id',
        'admin_user_id',
    ];

    const STATUS_ENABLE = 'enable';

    public function tagGroupTags()
    {
        return $this->hasMany(TagGroupTag::class, 'tag_id', 'id');
    }
}
