<?php

namespace Qz\Cores\Tag;

use Qz\Cores\Core;
use Qz\Cores\TagGroupTag\TagGroupTagAdd;
use Qz\Models\Tag;
use Qz\Models\TagGroupTag;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TagUpdate extends Core
{
    protected function execute()
    {
        $model = Tag::withTrashed()
            ->findOrFail($this->getId());
        $model->fill(Arr::whereNotNull([
            'name' => $this->getName(),
            'status' => $this->getStatus(),
        ]));
        $model->save();
        $this->setId($model->getKey());

        if ($this->getTagGroupIds()) {
            TagGroupTag::withTrashed()
                ->where('tag_id', $this->getId())
                ->delete();
            foreach ($this->getTagGroupIds() as $tagGroupId) {
                TagGroupTagAdd::init()
                    ->setTagId($this->getId())
                    ->setTagGroupId($tagGroupId)
                    ->run();
            }
        }
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
     * @return TagUpdate
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param $param
     * @return TagUpdate
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

    protected $name;

    protected $status;

    protected $tagGroupIds;

    /**
     * @return mixed
     */
    protected function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getTagGroupIds()
    {
        return $this->tagGroupIds;
    }

    /**
     * @param mixed $tagGroupIds
     */
    public function setTagGroupIds($tagGroupIds)
    {
        $this->tagGroupIds = $tagGroupIds;
        return $this;
    }
}
