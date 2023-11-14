<?php

namespace Qz\Http\Controllers\Admin\AdminUser;

use App\Exceptions\MessageException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Qz\Cores\AdminUser\AdminUserAdd;
use Qz\Cores\AdminUser\AdminUserDelete;
use Qz\Cores\AdminUser\AdminUserUpdate;
use Qz\Http\Controllers\Admin\AdminController;
use Qz\Models\AdminUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Qz\Models\AdminUserDepartment;

class AdminUserController extends AdminController
{
    public function get()
    {
        $model = AdminUser::query();
        $model = $this->filter($model);
        if ($this->getParam('admin_department_id')) {
            $model = $model->whereHas('adminUsers', function (Builder $builder) {
                $builder->whereHas('adminUserDepartments', function (Builder $builder) {
                    $builder->where('admin_department_id', $this->getParam('admin_department_id'));
                });
            });
        }
        $model = $model->orderByDesc('id')->get();
        $model->load([
            'adminUsers',
            'adminUsers.adminUserDepartments',
            'adminUsers.adminUserRoles'
        ]);
        $model->append(['statusDesc']);
        foreach ($model as &$item) {
            $adminDepartments = Arr::get($item, 'adminUsers.0.adminUserDepartments');
            $adminRoles = Arr::get($item, 'adminUsers.0.adminUserRoles');
            $department = [];
            foreach ($adminDepartments as $adminDepartment) {
                $department[] = [
                    'id' => Arr::get($adminDepartment, 'admin_department_id'),
                    'administrator' => Arr::get($adminDepartment, 'administrator')
                ];
            }
            $item->admin_departments = $department;
            $item->admin_role_ids = Arr::pluck($adminRoles, 'id');
        }
        return $this->success($model->toArray());
    }

    /**
     * @return JsonResponse
     */
    public function store()
    {
        $this->addParam('customer_id', $this->getCustomerId());
        $adminUserId = AdminUser::query()
            ->where('mobile', $this->getParam('mobile'))
            ->value('id');
        if ($adminUserId){
            $exist = AdminUserDepartment::query()
                ->where('admin_user_id', $adminUserId)
                ->exists();
            if ($exist){
                return $this->error('员工手机号不能重复');
            }
        }

        $id = AdminUserAdd::init()
            ->setParam($this->getParam())
            ->run()
            ->getId();
        return $this->success(compact('id'));
    }

    /**
     * @return JsonResponse
     */
    public function update()
    {
        $validator = Validator::make($this->getParam(), [
            'id' => [
                'required',
                Rule::exists(AdminUser::class)
                    ->withoutTrashed(),
            ],
            'mobile' => [
                Rule::unique(AdminUser::class)
                    ->withoutTrashed()
                    ->ignore($this->getParam('id'))
            ],
        ], [
            'id.required' => '请选择员工',
            'id.exists' => '员工id不存在',
            'mobile.unique' => '员工手机号不能重复',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $id = AdminUserUpdate::init()
            ->setId($this->getParam('id'))
            ->setParam($this->getParam())
            ->run()
            ->getId();
        return $this->success(compact('id'));
    }

    public function destroy()
    {
        $id = $this->getParam('id');
        if (is_array($id)) {
            foreach ($id as $value) {
                AdminUserDelete::init()
                    ->setId($value)
                    ->run()
                    ->getId();
            }
            return $this->success();
        }
        AdminUserDelete::init()
            ->setId($id)
            ->run()
            ->getId();
        return $this->success();
    }

    public function all()
    {
        $param = $this->getParam();
        $select = Arr::get($param, 'select', 'id as value, name as label');
        $model = AdminUser::query()
            ->selectRaw($select);
        $model = $this->filter($model);
        $model = $model->get();
        return $this->json($model);
    }

    public function allStatus()
    {
        $statusDesc = AdminUser::STATUS_DESC;
        $data = [];
        foreach ($statusDesc as $value => $label) {
            $data[] = compact('value', 'label');
        }
        return $this->json($data);
    }

    public function allSex()
    {
        $statusDesc = AdminUser::SEX_DESC;
        Arr::forget($statusDesc, [AdminUser::SEX_UNKNOWN]);
        $data = [];
        foreach ($statusDesc as $value => $label) {
            $data[] = compact('value', 'label');
        }
        return $this->json($data);
    }

    public function updatePassword()
    {
        $validator = Validator::make($this->getParam(), [
            'old_password' => [
                'required',
            ],
            'new_password' => [
                'required',
            ],
        ], [
            'old_password.required' => '请输入旧密码',
            'new_password.required' => '请输入新密码',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $adminUser = Auth::guard('admin')->user();
        $password = Arr::get($adminUser, 'password');
        if (!Hash::check($this->getParam('old_password'), $password)) {
            return $this->error("旧密码错误");
        }
        $id = AdminUserUpdate::init()
            ->setId(Arr::get($adminUser, 'id'))
            ->setPassword(Hash::make($this->getParam('new_password')))
            ->run()
            ->getId();
        return $this->success(compact('id'));
    }
}
