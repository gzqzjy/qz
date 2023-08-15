<?php

namespace Qz\Cores\AdminUserDepartment;

use Qz\Cores\Core;
use Qz\Models\AdminUserDepartment;

class AdminUserDepartmentDelete extends Core
{
    protected function execute()
    {
        $model = AdminUserDepartment::withTrashed()
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
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
