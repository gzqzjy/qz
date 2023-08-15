<?php

namespace Qz\Cores\AdminUser;

use Illuminate\Support\Arr;
use Qz\Cores\Core;
use Qz\Models\AdminDepartment;
use Qz\Models\AdminUser;
use Qz\Models\AdminUserDepartment;

class AdminDepartmentIdsByAdminUserIdGet extends Core
{
    protected function execute()
    {
        if (empty($this->getAdminUserId())) {
            return;
        }
        $adminUser = AdminUser::query()
            ->select(['id', 'customer_id'])
            ->find($this->getAdminUserId());
        if (empty($adminUser)) {
            return;
        }
        $adminUser->load('administrator');
        if (Arr::get($adminUser, 'administrator.id')) {
            $ids = AdminDepartment::query()
                ->where('customer_id', Arr::get($adminUser, 'customer_id'))
                ->pluck('id')
                ->toArray();
            if (!empty($ids)) {
                $this->ids = array_merge($this->ids, $ids);
            }
            return;
        }
        $ids = AdminUserDepartment::query()
            ->where('admin_user_id', $this->getAdminUserId())
            ->pluck('admin_department_id')
            ->toArray();
        if (!empty($ids)) {
            $this->ids = array_merge($this->ids, $ids);
        }
        $this->ids = array_unique($this->ids);
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
     * @return $this
     */
    public function setAdminUserId($adminUserId)
    {
        $this->adminUserId = $adminUserId;
        return $this;
    }

    protected $ids = [];

    /**
     * @return array
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * @param array $ids
     * @return $this
     */
    public function setIds($ids)
    {
        $this->ids = $ids;
        return $this;
    }
}
