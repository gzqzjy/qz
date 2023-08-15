<?php

namespace Qz\Cores\AdminMenu;

use Qz\Cores\Core;
use Qz\Models\AdminMenu;

class AdminMenuDelete extends Core
{
    protected function execute()
    {
        $model = AdminMenu::withTrashed()
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
     * @return AdminMenuDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
