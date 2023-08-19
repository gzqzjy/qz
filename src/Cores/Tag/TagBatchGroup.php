<?php

namespace Qz\Cores\Tag;

use Qz\Cores\Core;
use Qz\Cores\TagGroupTag\TagGroupTagAdd;

class TagBatchGroup extends Core
{
    protected function execute()
    {
        if ($this->getId()) {
            foreach ($this->getId() as $id) {
                TagGroupTagAdd::init()
                    ->setTagId($id)
                    ->setTagGroupId($this->getTagGroupId())
                    ->run();
            }
        }
    }

    protected $id;

    protected $tagGroupId;

    /**
     * @return mixed
     */
    protected function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $ids
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

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
}
