<?php

namespace Qz\Cores\AdminDepartment;

use Qz\Cores\Core;
use Qz\Models\AdminCategoryDepartment;
use Qz\Models\AdminDepartment;
use Qz\Models\AdminDepartmentRole;

class AdminDepartmentDelete extends Core
{
    protected function execute()
    {
        $model = AdminDepartment::withTrashed()
            ->findOrFail($this->getId());
        $model->delete();
        $this->setId($model->getKey());
        AdminCategoryDepartment::query()
            ->where('admin_department_id', $this->getId())
            ->delete();
        AdminDepartmentRole::query()
            ->where('admin_department_id', $this->getId())
            ->delete();
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
     * @return AdminDepartmentDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
