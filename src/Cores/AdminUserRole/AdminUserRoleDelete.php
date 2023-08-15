<?php

namespace Qz\Cores\AdminUserRole;

use Qz\Cores\Core;
use Qz\Models\AdminUserRole;

class AdminUserRoleDelete extends Core
{
    protected function execute()
    {
        $model = AdminUserRole::withTrashed()
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
