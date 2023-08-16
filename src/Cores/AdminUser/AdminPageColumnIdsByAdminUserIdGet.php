<?php

namespace Qz\Cores\AdminUser;

use Illuminate\Support\Arr;
use Qz\Cores\Core;
use Qz\Models\AdminUser;

class AdminPageColumnIdsByAdminUserIdGet extends Core
{
    protected function execute()
    {
        if (empty($this->getAdminUserId())) {
            return;
        }
        $model = AdminUser::query()
            ->select(['id'])
            ->find($this->getAdminUserId());
        if (empty($model)) {
            return;
        }
        $model->load([
            'adminUserRoles',
            'adminUserRoles.adminRole',
            'adminUserRoles.adminRole.adminRolePageColumns',
        ]);
        $adminUserRoles = Arr::get($model, 'adminUserRoles');
        foreach ($adminUserRoles as $adminUserRole) {
            $adminRole = Arr::get($adminUserRole, 'adminRole');
            if (empty($adminRole)) {
                continue;
            }
            $adminRolePageColumns = Arr::get($adminRole, 'adminRolePageColumns');
            foreach ($adminRolePageColumns as $adminRolePageColumn) {
                $this->adminPageColumnIds[] = (int) Arr::get($adminRolePageColumn, 'admin_page_column_id');
            }
        }
        $this->adminPageColumnIds = array_unique(array_values($this->adminPageColumnIds));
    }

    protected $adminPageColumnIds = [];

    /**
     * @return mixed
     */
    public function getAdminPageColumnIds()
    {
        return $this->adminPageColumnIds;
    }

    /**
     * @param mixed $adminPageColumnIds
     * @return $this
     */
    public function setAdminPageColumnIds($adminPageColumnIds)
    {
        $this->adminPageColumnIds = $adminPageColumnIds;
        return $this;
    }

    protected $adminUserId;

    /**
     * @return mixed
     */
    public function getAdminUserId()
    {
        return $this->adminUserId;
    }

    /**
     * @param mixed $adminUserId
     * @return AdminPageColumnIdsByAdminUserIdGet
     */
    public function setAdminUserId($adminUserId)
    {
        $this->adminUserId = $adminUserId;
        return $this;
    }
}
