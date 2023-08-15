<?php

namespace Qz\Cores\Category;

use Qz\Cores\Core;
use Qz\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CategoryAdd extends Core
{
    protected function execute()
    {
        $model = Category::withTrashed()
            ->firstOrCreate(Arr::whereNotNull([
                'name' => $this->getName(),
                'customer_id' => $this->getCustomerId(),
            ]));
        if ($model->trashed()) {
            $model->restore();
        }
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
     * @return CategoryAdd
     */
    protected function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param $param
     * @return CategoryAdd
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
     * @return CategoryAdd
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    protected $customerId;

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param mixed $customerId
     * @return CategoryAdd
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
        return $this;
    }
}
