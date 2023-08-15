<?php

namespace Qz\Http\Controllers\Admin\Category;

use Illuminate\Database\Eloquent\Builder;
use Qz\Http\Controllers\Admin\AdminController;
use Qz\Models\Category;

class CategoryController extends AdminController
{
    public function all()
    {
        $model = Category::query();
        if (!$this->isAdministrator()) {
            $model->whereHas('adminCategoryDepartment', function (Builder $builder) {
                $builder->whereHas('adminDepartment', function (Builder $builder) {
                    $builder->whereHas('adminUserDepartments', function (Builder $builder) {
                        $builder->where('admin_user_id', $this->getLoginAdminUserId());
                    });
                });
            });
        }
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
