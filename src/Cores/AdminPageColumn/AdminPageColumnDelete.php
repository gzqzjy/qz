<?php

namespace Qz\Cores\AdminPageColumn;

use Qz\Cores\Core;
use Qz\Models\AdminPageColumn;

class AdminPageColumnDelete extends Core
{
    protected function execute()
    {
        $model = AdminPageColumn::withTrashed()
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
     * @return AdminPageColumnDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
