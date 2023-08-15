<?php

namespace Qz\Cores\AdminRole;

use Qz\Cores\Core;
use Qz\Models\AdminDepartmentRole;
use Qz\Models\AdminRoleMenu;
use Qz\Models\AdminRolePageColumn;
use Qz\Models\AdminRolePageOption;
use Qz\Models\AdminRole;
use Qz\Models\AdminUserRole;


class AdminRoleDelete extends Core
{
    protected function execute()
    {
        $model = AdminRole::withTrashed()
            ->findOrFail($this->getId());
        $model->delete();
        $this->setId($model->getKey());
        AdminDepartmentRole::query()
            ->where('admin_role_id', $this->getId())
            ->delete();
        AdminUserRole::query()
            ->where('admin_role_id', $this->getId())
            ->delete();
        AdminRoleMenu::query()
            ->where('admin_role_id', $this->getId())
            ->delete();
        AdminRolePageColumn::query()
            ->where('admin_role_id', $this->getId())
            ->delete();
        AdminRolePageOption::query()
            ->where('admin_role_id', $this->getId())
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
     * @return AdminRoleDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
