<?php

namespace Qz\Cores\AdminUser;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Qz\Cores\Core;
use Qz\Models\AdminDepartment;
use Qz\Models\AdminRequest;
use Qz\Models\AdminRoleRequest;
use Qz\Models\AdminUser;
use Qz\Models\AdminUserDepartment;

class AdminUserIdsByAdminUserIdGet extends Core
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
        $ids = AdminUser::query()
            ->where('customer_id', Arr::get($adminUser, 'customer_id'))
            ->pluck('id')
            ->toArray();
        $this->ids[] = 0;
        if (!empty($ids)) {
            $this->ids = array_unique(array_merge($this->ids, $ids));
        }
        return;
    }

    protected function getIdsByAdminRoleRequests($adminRoleRequests)
    {
        $types = [];
        foreach ($adminRoleRequests as $adminRoleRequest) {
            $types = array_merge($types, Arr::get($adminRoleRequest, 'types'));
        }
        $this->getIdsByTypes($types);
    }

    protected function getIdsByTypes($types = [])
    {
        $types = array_unique($types);
        foreach ($types as $type) {
            if ($type == AdminRequest::SELF) {
                $this->ids[] = $this->getAdminUserId();
            } elseif ($type == AdminRequest::UNDEFINED) {
                $this->ids[] = 0;
            } elseif ($type == AdminRequest::THIS) {
                $adminDepartmentIds = AdminDepartmentIdsByAdminUserIdGet::init()
                    ->setAdminUserId($this->getAdminUserId())
                    ->run()
                    ->getIds();
                $ids = AdminUserDepartment::query()
                    ->where('admin_user_id', '!=', $this->getAdminUserId())
                    ->whereIn('admin_department_id', $adminDepartmentIds)
                    ->pluck('admin_user_id')
                    ->toArray();
                $this->ids = array_merge($this->ids, $ids);
            } elseif ($type == AdminRequest::PEER) {
                $adminDepartmentIds = AdminDepartmentIdsByAdminUserIdGet::init()
                    ->setAdminUserId($this->getAdminUserId())
                    ->run()
                    ->getIds();
                $ids = AdminUserDepartment::query()
                    ->where('admin_user_id', '!=', $this->getAdminUserId())
                    ->whereHas('adminDepartment', function (Builder $builder) use ($adminDepartmentIds) {
                        $builder->whereIn('pid', $adminDepartmentIds);
                    })
                    ->pluck('admin_user_id')
                    ->toArray();
                $this->ids = array_merge($this->ids, $ids);
            } elseif ($type == AdminRequest::CHILDREN) {
                $adminDepartmentIds = AdminDepartmentIdsByAdminUserIdGet::init()
                    ->setAdminUserId($this->getAdminUserId())
                    ->run()
                    ->getIds();
                $adminDepartments = AdminDepartment::query()
                    ->select(['id', 'pid'])
                    ->with('children')
                    ->whereIn('pid', $adminDepartmentIds)
                    ->get();
                $adminDepartmentIds = [];
                $adminDepartmentIds = $this->getAllAdminDepartmentIds($adminDepartments, $adminDepartmentIds);
                $ids = AdminUserDepartment::query()
                    ->whereIn('admin_department_id', $adminDepartmentIds)
                    ->where('admin_user_id', '!=', $this->getAdminUserId())
                    ->pluck('admin_user_id')
                    ->toArray();
                $this->ids = array_merge($this->ids, $ids);
            } elseif ($type == AdminRequest::ALL) {
                $ids = AdminUser::query()
                    ->pluck('id')
                    ->toArray();
                $ids[] = 0;
                $this->ids = array_merge($this->ids, $ids);
            }
        }
        $this->ids = array_unique($this->ids);
    }

    protected function getAllAdminDepartmentIds($items, $ids = [])
    {
        if (empty($items)) {
            return $ids;
        }
        foreach ($items as $item) {
            $ids[] = Arr::get($item, 'id');
            $children = Arr::get($item, 'children');
            if ($children && count($children)) {
                $ids = $this->getAllAdminDepartmentIds($children, $ids);
            }
        }
        return $ids;
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

    protected $adminRequestId;

    /**
     * @return mixed
     */
    public function getAdminRequestId()
    {
        return $this->adminRequestId;
    }

    /**
     * @param mixed $adminRequestId
     * @return AdminUserIdsByAdminUserIdGet
     */
    public function setAdminRequestId($adminRequestId)
    {
        $this->adminRequestId = $adminRequestId;
        return $this;
    }

    protected $types = [];

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param array $types
     * @return AdminUserIdsByAdminUserIdGet
     */
    public function setTypes($types)
    {
        $this->types = $types;
        return $this;
    }
}
