<?php

namespace Qz\Cores\AdminUser;

use Qz\Cores\Core;
use Qz\Models\AdminUser;

class AdminUserDelete extends Core
{
    protected function execute()
    {
        $model = AdminUser::withTrashed()
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
     * @return AdminUserDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
