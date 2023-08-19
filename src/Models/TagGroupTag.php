<?php

namespace Qz\Models;

class TagGroupTag extends Model
{
    protected $fillable = [
        'tag_id',
        'tag_group_id',
    ];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function tagGroup()
    {
        return $this->belongsTo(TagGroup::class);
    }
}
