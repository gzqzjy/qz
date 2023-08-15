<?php

namespace Qz\Http\Controllers\Admin\Category;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Qz\Cores\Category\CategoryAdd;
use Qz\Cores\Category\CategoryDelete;
use Qz\Cores\Category\CategoryUpdate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Qz\Http\Controllers\Admin\AdminController;
use Qz\Models\Category;

class CategoryController extends AdminController
{
    public function get()
    {
        $model = Category::query();
        $model->whereIn('id', $this->getLoginCategoryIdes());
        $model = $this->filter($model);
        $model = $model->orderByDesc('id')
            ->paginate($this->getParam('page_size'));
        $data = [
            'data' => $model->items(),
            'total' => $model->total(),
            'page_size' => $model->perPage(),
            'current' => $model->currentPage(),
        ];
        return $this->json($data);
    }

    public function store()
    {
        $validator = Validator::make($this->getParam(), [
            'name' => [
                'required',
                Rule::unique(Category::class)
                    ->where('customer_id', $this->getCustomerId())
                    ->withoutTrashed()
            ],
        ], [
            'name.required' => '品类名称不能为空',
            'name.unique' => '品类已存在',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $id = CategoryAdd::init()
            ->setParam($this->getParam())
            ->setCustomerId($this->getCustomerId())
            ->run()
            ->getId();
        return $this->success(compact('id'));
    }

    public function update()
    {
        $validator = Validator::make($this->getParam(), [
            'name' => [
                'required',
                Rule::unique(Category::class)
                    ->where('customer_id', $this->getCustomerId())
                    ->withoutTrashed()
                    ->ignore($this->getParam('id')),
            ],
        ], [
            'name.required' => '品类名称不能为空',
            'name.unique' => '品类已存在',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $param = $this->getParam();
        $id = CategoryUpdate::init()
            ->setParam($param)
            ->setId($this->getParam('id'))
            ->run()
            ->getId();
        return $this->success(compact('id'));
    }

    public function destroy()
    {
        $param = $this->getParam();
        if (is_array(Arr::get($param, 'id'))) {
            $ids = Arr::get($param, 'id');
            foreach ($ids as $id) {
                CategoryDelete::init()
                    ->setId($id)
                    ->run();
            }
        } else {
            CategoryDelete::init()
                ->setId(Arr::get($param, 'id'))
                ->run();
        }
        return $this->success([]);
    }

    public function all()
    {
        $model = Category::query();
        $model->whereIn('id', $this->getLoginCategoryIdes());
        $model = $this->filter($model);
        if ($this->getParam('select')) {
            $model->selectRaw($this->getParam('select'));
        }
        if ($this->getParam('admin_department_id')) {
            $adminDepartmentId = $this->getParam('admin_department_id');
            $model->whereHas('adminCategoryDepartments', function (Builder $builder) use ($adminDepartmentId) {
                $builder->where('admin_department_id', $adminDepartmentId);
            });
        }
        $model = $model->get();
        return $this->response($model);
    }
}
