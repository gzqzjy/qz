<?php

namespace Qz\Cores\AdminRoleGroup;

use Qz\Cores\Core;
use Qz\Models\AdminRoleGroup;


class AdminRoleGroupDelete extends Core
{
    protected function execute()
    {
        $model = AdminRoleGroup::withTrashed()
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
     * @return AdminRoleGroupDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
