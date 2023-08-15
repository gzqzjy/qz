<?php

namespace Qz\Cores\AdminUserPageOption;

use Qz\Cores\Core;
use Qz\Models\AdminUserPageOption;

class AdminUserPageOptionDelete extends Core
{
    protected function execute()
    {
        $model = AdminUserPageOption::withTrashed()
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
     * @return AdminUserPageOptionDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
