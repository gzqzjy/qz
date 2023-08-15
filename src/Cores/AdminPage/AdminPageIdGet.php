<?php

namespace Qz\Cores\AdminPage;

use Qz\Cores\Core;
use Qz\Models\AdminPage;

class AdminPageIdGet extends Core
{
    protected function execute()
    {
        if (empty($this->getCode())) {
            return;
        }
        $model = AdminPage::query()
            ->where('code', $this->getCode())
            ->first();
        if (empty($model)) {
            return;
        }
        $this->setId($model->getKey());
    }

    protected $code;

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
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
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
