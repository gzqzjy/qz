<?php

namespace Qz\Cores\AdminUser;

use Illuminate\Support\Arr;
use Qz\Cores\Core;
use Qz\Models\AdminMenu;
use Qz\Models\AdminUser;

class AdminMenuIdsByAdminUserIdGet extends Core
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
        $model->load('administrator');
        if (Arr::get($model, 'administrator.id')) {
            $this->adminMenuIds = AdminMenu::query()
                ->where('customer_id', Arr::get($model, 'customer_id'))
                ->pluck('id')
                ->toArray();
            return;
        }
        $model->load([
            'adminUserRoles',
            'adminUserRoles.adminRole',
            'adminUserRoles.adminRole.adminRoleMenus',
        ]);
        $adminUserRoles = Arr::get($model, 'adminUserRoles');
        foreach ($adminUserRoles as $adminUserRole) {
            $adminRole = Arr::get($adminUserRole, 'adminRole');
            if (empty($adminRole)) {
                continue;
            }
            $adminRoleMenus = Arr::get($adminRole, 'adminRoleMenus');
            foreach ($adminRoleMenus as $adminRoleMenu) {
                $this->adminMenuIds[] = Arr::get($adminRoleMenu, 'admin_menu_id');
            }
        }
        $this->adminMenuIds = array_unique(array_values($this->adminMenuIds));
    }

    protected $adminMenuIds = [];

    /**
     * @return mixed
     */
    public function getAdminMenuIds()
    {
        return $this->adminMenuIds;
    }

    /**
     * @param mixed $adminMenuIds
     * @return $this
     */
    public function setAdminMenuIds($adminMenuIds)
    {
        $this->adminMenuIds = $adminMenuIds;
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
     * @return AdminMenuIdsByAdminUserIdGet
     */
    public function setAdminUserId($adminUserId)
    {
        $this->adminUserId = $adminUserId;
        return $this;
    }
}
