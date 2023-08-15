<?php

namespace Qz\Http\Controllers\Admin\AdminPageOption;

use Illuminate\Support\Arr;
use Qz\Http\Controllers\Admin\AdminController;
use Qz\Models\AdminPageOption;

class AdminPageOptionController extends AdminController
{
    public function all()
    {
        $param = $this->getParam();
        $select = Arr::get($param, 'select', 'id as value, name as label');
        $model = AdminPageOption::query()
            ->selectRaw($select);
        $model = $this->filter($model);
        $model = $model->get();
        return $this->response($model);
    }
}
