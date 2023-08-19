<?php

namespace Qz\Cores\Tag;

use Qz\Cores\Core;
use Qz\Models\Tag;

class TagDelete extends Core
{
    protected function execute()
    {
        $model = Tag::withTrashed()
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
     * @return TagDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
