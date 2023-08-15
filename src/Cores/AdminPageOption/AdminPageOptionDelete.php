<?php

namespace Qz\Cores\AdminPageOption;

use Qz\Cores\Core;
use Qz\Models\AdminPageOption;

class AdminPageOptionDelete extends Core
{
    protected function execute()
    {
        $model = AdminPageOption::withTrashed()
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
     * @return AdminPageOptionDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
