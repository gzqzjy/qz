<?php

namespace Qz\Cores\TagGroupTag;

use Qz\Cores\Core;
use Qz\Models\TagGroupTag;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TagGroupTagAdd extends Core
{
    protected function execute()
    {
        $model = TagGroupTag::withTrashed()
            ->updateOrCreate(Arr::whereNotNull([
                'tag_group_id' => $this->getTagGroupId(),
                'tag_id' => $this->getTagId(),
            ]));
        if ($model->trashed()) {
            $model->restore();
        }
        $this->setId($model->getKey());
    }

    protected $id;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return TagGroupTagAdd
     */
    protected function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param $param
     * @return TagGroupTagAdd
     */
    public function setParam($param)
    {
        foreach ($param as $key => $value) {
            $setMethod = 'set' . Str::studly($key);
            if (method_exists($this, $setMethod)) {
                call_user_func([$this, $setMethod], $value);
            }
        }
        return $this;
    }

    protected $tagGroupId;

    protected $tagId;

    /**
     * @return mixed
     */
    protected function getTagGroupId()
    {
        return $this->tagGroupId;
    }

    /**
     * @param mixed $tagGroupId
     */
    public function setTagGroupId($tagGroupId)
    {
        $this->tagGroupId = $tagGroupId;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getTagId()
    {
        return $this->tagId;
    }

    /**
     * @param mixed $tagId
     */
    public function setTagId($tagId)
    {
        $this->tagId = $tagId;
        return $this;
    }
}
