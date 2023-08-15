<?php

namespace Qz\Http\Controllers\Admin\AdminPageColumn;

use Illuminate\Support\Arr;
use Qz\Http\Controllers\Admin\AdminController;
use Qz\Models\AdminPageColumn;

class AdminPageColumnController extends AdminController
{
    public function all()
    {
        $param = $this->getParam();
        $select = Arr::get($param, 'select', 'id as value, name as label');
        $model = AdminPageColumn::query()
            ->selectRaw($select)
            ->whereHas('adminPage');
        $model = $this->filter($model);
        $model = $model->get();
        return $this->response($model);
    }
}
