<?php

namespace Qz\Cores\AdminRoleMenu;

use Qz\Cores\Core;
use Qz\Models\AdminRoleMenu;

class AdminRoleMenuDelete extends Core
{
    protected function execute()
    {
        $model = AdminRoleMenu::withTrashed()
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
     * @return AdminRoleMenuDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
