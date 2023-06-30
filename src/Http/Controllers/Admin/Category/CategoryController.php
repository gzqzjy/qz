<?php
namespace Qz\Admin\Permission\Http\Controllers\Admin\Category;

use Illuminate\Database\Eloquent\Builder;
use Qz\Admin\Permission\Cores\AdminDepartment\GetInfoByAdminUserCustomerSubsystemId;
use Qz\Admin\Permission\Facades\Access;
use Qz\Admin\Permission\Http\Controllers\Admin\AdminController;
use Qz\Admin\Permission\Models\AdminCategoryDepartment;
use Qz\Admin\Permission\Models\AdminDepartmentRole;
use Qz\Admin\Permission\Models\AdminUserCustomerSubsystemDepartment;
use Qz\Admin\Permission\Models\Category;

class CategoryController extends AdminController
{
    public function all()
    {
        $model = Category::query();
        $model = $this->filter($model);
        if ($this->getParam('select')) {
            $model->selectRaw($this->getParam('select'));
        }
        if ($this->getParam('admin_department_id')){
            $adminDepartmentId = $this->getParam('admin_department_id');
            $model->whereHas('adminCategoryDepartments', function (Builder $builder) use ($adminDepartmentId){
                $builder->where('admin_department_id', $adminDepartmentId);
            });
        }
        if (empty($this->isAdministrator())){
            //获取本部门下的品类
            $categoryIds = GetInfoByAdminUserCustomerSubsystemId::init()
                ->setAdminUserCustomerSubsystemId(Access::getAdminUserCustomerSubsystemId())
                ->getCategoryIds();
            if ($categoryIds){
                $model->whereIn('id', $categoryIds);
            }
        }
        $model = $model->get();
        return $this->response($model);
    }
}
