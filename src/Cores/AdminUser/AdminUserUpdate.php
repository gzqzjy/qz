<?php

namespace Qz\Cores\AdminUser;

use Qz\Cores\AdminUserDepartment\AdminUserDepartmentSync;
use Qz\Cores\AdminUserRole\AdminUserRoleSync;
use Qz\Cores\Core;
use Qz\Models\AdminUser;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AdminUserUpdate extends Core
{
    protected function execute()
    {
        $update = Arr::whereNotNull([
            'name' => $this->getName(),
            'mobile' => $this->getMobile(),
            'status' => $this->getStatus(),
            'sex' => $this->getSex(),
        ]);
        if (!empty($update)) {
            $model = AdminUser::withTrashed()
                ->findOrFail($this->getId());
            $model->fill($update);
            $model->save();
            $this->setId($model->getKey());
        }
        AdminUserRoleSync::init()
            ->setAdminUserId($this->getId())
            ->setAdminRoleIds($this->getAdminRoleIds())
            ->run();
        AdminUserDepartmentSync::init()
            ->setAdminUserId($this->getId())
            ->setAdminUserDepartments($this->getAdminUserDepartments())
            ->run();
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
     * @return AdminUserUpdate
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param $param
     * @return AdminUserUpdate
     */
    public function setParam($param)
    {
        foreach ($param as $key => $value) {
            $setMethod = 'set' . Str::studly($key);
            if (method_exists($this, $setMethod)) {
                call_user_func([$this, $setMethod], $value);
            }
        }
        return $this;
    }

    protected $name;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return AdminUserUpdate
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    protected $mobile;

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param mixed $mobile
     * @return AdminUserUpdate
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
        return $this;
    }

    protected $status;

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     * @return AdminUserUpdate
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    protected $sex;

    /**
     * @return mixed
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param mixed $sex
     * @return AdminUserUpdate
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
        return $this;
    }

    protected $adminRoleIds;

    /**
     * @return mixed
     */
    public function getAdminRoleIds()
    {
        return $this->adminRoleIds;
    }

    /**
     * @param mixed $adminRoleIds
     * @return AdminUserUpdate
     */
    public function setAdminRoleIds($adminRoleIds)
    {
        $this->adminRoleIds = $adminRoleIds;
        return $this;
    }

    protected $adminUserDepartments;

    /**
     * @return mixed
     */
    public function getAdminUserDepartments()
    {
        return $this->adminUserDepartments;
    }

    /**
     * @param mixed $adminUserDepartments
     * @return AdminUserUpdate
     */
    public function setAdminUserDepartments($adminUserDepartments)
    {
        $this->adminUserDepartments = $adminUserDepartments;
        return $this;
    }
}
