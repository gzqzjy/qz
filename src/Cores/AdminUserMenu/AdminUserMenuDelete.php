<?php

namespace Qz\Cores\AdminUserMenu;

use Qz\Cores\Core;
use Qz\Models\AdminUserMenu;

class AdminUserMenuDelete extends Core
{
    protected function execute()
    {
        $model = AdminUserMenu::withTrashed()
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
     * @return AdminUserMenuDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
