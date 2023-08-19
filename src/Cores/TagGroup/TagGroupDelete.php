<?php

namespace Qz\Cores\TagGroup;

use Qz\Cores\Core;
use Qz\Models\TagGroup;

class TagGroupDelete extends Core
{
    protected function execute()
    {
        $model = TagGroup::withTrashed()
            ->findOrFail($this->getId());
        $model->delete();
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
     * @return TagGroupDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
