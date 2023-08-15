<?php

namespace Qz\Cores\AdminPage;

use Qz\Cores\Core;
use Qz\Models\AdminPage;

class AdminPageDelete extends Core
{
    protected function execute()
    {
        $model = AdminPage::withTrashed()
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
     * @return AdminPageDelete
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
