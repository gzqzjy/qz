<?php

namespace Qz\Models;

class AdminRequest extends Model
{
    protected $fillable = [
        'admin_page_option_id',
        'name',
        'code'
    ];

    const SELF = 'SELF'; //自己
    const THIS = 'THIS'; //本部门
    const PEER = 'PEER'; //同级部门
    const CHILDREN = 'CHILDREN'; //下级部门
    const UNDEFINED = 'UNDEFINED'; //其他
    const ALL = 'ALL'; //所有
    const TYPE_SELF = self::SELF;
    const TYPE_THIS = self::THIS;
    const TYPE_PEER = self::PEER;
    const TYPE_CHILDREN = self::CHILDREN;
    const TYPE_UNDEFINED = self::UNDEFINED;
    const TYPE_ALL = self::ALL;
    const TYPE_BASE_DESC = [
        self::SELF => "自己",
        self::THIS => "本部门",
        self::PEER => "同级部门",
        self::CHILDREN => "下级部门",
        self::UNDEFINED => "其他",
        self::ALL => "所有",
    ];

    public function adminPageOption()
    {
        return $this->belongsTo(AdminPageOption::class);
    }
}
