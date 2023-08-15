<?php

namespace Qz\Http\Controllers\Admin\AdminPage;

use Qz\Cores\AdminPage\AdminPageAdd;
use Qz\Http\Controllers\Admin\AdminController;
use Qz\Models\AdminMenu;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AdminPageController extends AdminController
{
    /**
     * @return JsonResponse
     */
    public function store()
    {
        $validator = Validator::make($this->getParam(), [
            'name' => [
                'required',
            ],
            'code' => [
                'required',
            ],
        ], [
            'name' => [
                'required' => '页面名不能为空',
            ],
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $id = AdminPageAdd::init()
            ->setParam($this->getParam())
            ->run()
            ->getId();
        return $this->success(compact('id'));
    }

    public function permission()
    {
        $model = AdminMenu::query()
            ->where('parent_id', 0)
            ->orderByDesc('sort')
            ->get();
        $model->load([
            'children',
            'adminPageOptions',
            'adminPageColumns',
        ]);
        return $this->success($model);
    }
}
