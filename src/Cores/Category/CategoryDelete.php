<?php

namespace Qz\Cores\Category;

use Qz\Cores\Core;
use Qz\Models\Category;

class CategoryDelete extends Core
{
    protected function execute()
    {
        $model = Category::withTrashed()
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
     * @return CategoryDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
