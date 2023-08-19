<?php

namespace Qz\Cores\Tag;

use Qz\Cores\Core;
use Qz\Cores\TagGroupTag\TagGroupTagAdd;
use Qz\Models\Tag;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TagAdd extends Core
{
    protected function execute()
    {
        $model = Tag::withTrashed()
            ->create(Arr::whereNotNull([
                'name' => $this->getName(),
                'status' => $this->getStatus(),
                'customer_id' => $this->getCustomerId(),
                'admin_user_id' => $this->getAdminUserId(),
            ]));
        if ($model->trashed()) {
            $model->restore();
        }
        $this->setId($model->getKey());

        if ($this->getTagGroupIds()) {
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
     * @return TagAdd
     */
    protected function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param $param
     * @return TagAdd
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

    protected $customerId;

    protected $adminUserId;

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
    protected function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param mixed $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * @return mixed
     */
    protected function getAdminUserId()
    {
        return $this->adminUserId;
    }

    /**
     * @param mixed $adminUserId
     */
    public function setAdminUserId($adminUserId)
    {
        $this->adminUserId = $adminUserId;
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
