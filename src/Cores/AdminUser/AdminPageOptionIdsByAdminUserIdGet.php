<?php

namespace Qz\Cores\AdminUser;

use Illuminate\Support\Arr;
use Qz\Cores\Core;
use Qz\Models\AdminUser;

class AdminPageOptionIdsByAdminUserIdGet extends Core
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
            'adminUserRoles.adminRole.adminRolePageOptions',
        ]);
        $adminUserRoles = Arr::get($model, 'adminUserRoles');
        foreach ($adminUserRoles as $adminUserRole) {
            $adminRole = Arr::get($adminUserRole, 'adminRole');
            if (empty($adminRole)) {
                continue;
            }
            $adminRolePageOptions = Arr::get($adminRole, 'adminRolePageOptions');
            foreach ($adminRolePageOptions as $adminRolePageOption) {
                $this->adminPageOptionIds[] = Arr::get($adminRolePageOption, 'admin_page_option_id');
            }
        }
        $this->adminPageOptionIds = array_unique(array_values($this->adminPageOptionIds));
    }

    protected $adminPageOptionIds = [];

    /**
     * @return mixed
     */
    public function getAdminPageOptionIds()
    {
        return $this->adminPageOptionIds;
    }

    /**
     * @param mixed $adminPageOptionIds
     * @return $this
     */
    public function setAdminPageOptionIds($adminPageOptionIds)
    {
        $this->adminPageOptionIds = $adminPageOptionIds;
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
     * @return AdminPageOptionIdsByAdminUserIdGet
     */
    public function setAdminUserId($adminUserId)
    {
        $this->adminUserId = $adminUserId;
        return $this;
    }
}
