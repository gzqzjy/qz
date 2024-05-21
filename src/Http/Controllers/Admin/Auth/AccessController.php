<?php

namespace Qz\Http\Controllers\Admin\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Qz\Cores\AdminPage\AdminPageAdd;
use Qz\Cores\AdminPage\AdminPageIdGet;
use Qz\Cores\AdminPageColumn\AdminPageColumnAdd;
use Qz\Cores\AdminPageOption\AdminPageOptionAdd;
use Qz\Cores\AdminPageOption\AdminPageOptionDelete;
use Qz\Cores\AdminUser\AdminMenuIdsByAdminUserIdGet;
use Qz\Cores\AdminUser\AdminPageColumnIdsByAdminUserIdGet;
use Qz\Cores\AdminUser\AdminPageOptionIdsByAdminUserIdGet;
use Qz\Cores\AdminUser\AdminUserUpdate;
use Qz\Http\Controllers\Admin\AdminController;
use Qz\Models\AdminMenu;
use Qz\Models\AdminPage;
use Qz\Models\AdminPageColumn;
use Qz\Models\AdminPageOption;
use Qz\Models\AdminUser;
use Qz\Models\AdminUserPageOption;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccessController extends AdminController
{
    public function login()
    {
        $status = 'error';
        $type = 'mobile';
        $mobile = $this->getParam('mobile');
        $token = '';
        $model = AdminUser::query()
            ->where('mobile', $mobile)
            ->where('status', AdminUser::STATUS_WORKING)
            ->orderBy('id')
            ->first();
        if (empty($model)) {
            $status = 'mobileError';
            return $this->json(compact('token', 'status', 'type'));
        }
        if (!Hash::check($this->getParam('password'), $model->getOriginal('password'))){
            $status = 'passwordError';
            return $this->json(compact('token', 'status', 'type'));
        }

        Log::info("默认密码", [config('common.default_admin_user_password'), $model->getOriginal('password'), Hash::check(config('common.default_admin_user_password'), $model->getOriginal('password'))]);
        if (Hash::check(config('common.default_admin_user_password'), $model->getOriginal('password'))){
            if (empty($this->getParam('new_password'))){
                $status = 'resetPassword';
                return $this->json(compact('token', 'status', 'type'));
            }
            if ($this->getParam('new_password') != $this->getParam('new_password_confirm')){
                $status = 'differentPassword';
                return $this->json(compact('token', 'status', 'type'));
            }
            if ($this->getParam('new_password') == config('common.default_admin_user_password')){
                $status = 'defaultPassword';
                return $this->json(compact('token', 'status', 'type'));
            }
            if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $this->getParam('new_password'))) {
                $status = 'verifyFaildPassword';//密码验证失败，必须包含字母和数字，并且长度至少为8位
                return $this->json(compact('token', 'status', 'type'));
            }
            AdminUserUpdate::init()
                ->setId(Arr::get($model, 'id'))
                ->setPassword(Hash::make($this->getParam('new_password')))
                ->run();
        }


        if ($model instanceof AdminUser) {
            $model->setConnection(config('database.default'))->tokens()->delete();
            $token = $model->setConnection(config('database.default'))->createToken('admin_user')->plainTextToken;
            if ($token) {
                $status = 'ok';
            }
        }
        return $this->json(compact('token', 'status', 'type'));
    }

    /**
     * @return JsonResponse
     */
    public function captcha()
    {
        $mobile = $this->getParam('phone');
        if (empty($mobile)) {
            return $this->error('手机号不能为空');
        }
        return $this->success();
    }

    public function logout()
    {
        $user = Auth::guard('admin')
            ->user();
        if ($user instanceof AdminUser) {
            $user->setConnection(config('database.default'))->tokens()->delete();
        }
        return $this->success();
    }

    public function user()
    {
        $user = Auth::guard('admin')->user();
        $name = '';
        if ($user instanceof AdminUser) {
            $id = Arr::get($user, 'id', '');
            $name = Arr::get($user, 'name', '');
            $administrator = $this->isAdministrator();
        }
        return $this->success(compact('id', 'name', 'administrator'));
    }

    public function addPage()
    {
        $pageId = AdminPageAdd::init()
            ->setName($this->getParam('page_name'))
            ->setCode($this->getParam('page_code'))
            ->run()
            ->getId();
        return $this->success(compact('pageId'));
    }

    public function columns()
    {
        $pageId = AdminPageIdGet::init()
            ->setCode($this->getParam('page_code'))
            ->run()
            ->getId();
        $columns = $this->getParam('columns');
        $pageColumnIds = [];
        foreach ($columns as $column) {
            $dataIndex = Arr::get($column, 'data_index');
            if (is_array($dataIndex)) {
                $dataIndex = implode('.', $dataIndex);
            }
            $pageColumnId = AdminPageColumnAdd::init()
                ->setAdminPageId($pageId)
                ->setCode($dataIndex)
                ->setName(Arr::get($column, 'title'))
                ->run()
                ->getId();
            if ($pageColumnId) {
                $pageColumnIds = Arr::prepend($pageColumnIds, $pageColumnId);
            }
        }
        $model = AdminPageColumn::query()
            ->whereIn('id', $pageColumnIds);
        if (!$this->isAdministrator()) {
            $adminPageColumnIds = AdminPageColumnIdsByAdminUserIdGet::init()
                ->setAdminUserId($this->getLoginAdminUserId())
                ->run()
                ->getAdminPageColumnIds();
            $model->whereIn('id',  (array) $adminPageColumnIds);
        }
        $dataIndexes = $model
            ->pluck('code');
        return $this->json(compact('dataIndexes'));
    }

    /**
     * @return JsonResponse
     */
    public function option()
    {
        $access = false;
        $validator = Validator::make($this->getParam(), [
            'page_code' => [
                'required',
                Rule::exists(AdminPage::class, 'code')
                    ->withoutTrashed(),
            ],
            'option_code' => [
                'required',
            ],
            'option_name' => [
                'required',
            ],
        ], [
            'page_code.required' => '页面标识不能为空',
            'page_code.exists' => '页面标识不存在',
            'option_code.required' => '页面操作标识不能为空',
            'option_name.required' => '页面操作标识不能为空',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $pageId = AdminPageIdGet::init()
            ->setCode($this->getParam('page_code'))
            ->run()
            ->getId();
        if (empty($pageId)) {
            return $this->json($access);
        }
        $pageOptionId = AdminPageOptionAdd::init()
            ->setAdminPageId($pageId)
            ->setCode($this->getParam('option_code'))
            ->setName($this->getParam('option_name'))
            ->run()
            ->getId();
        if (empty($pageOptionId)) {
            return $this->json($access);
        }
        if ($this->isAdministrator()) {
            $access = true;
            return $this->json($access);
        }
        $access = AdminUserPageOption::query()
            ->whereHas('adminUser', function (Builder $builder) {
                $builder->where('admin_user_id',$this->getLoginAdminUserId());
            })
            ->where('admin_page_option_id', $pageOptionId)
            ->exists();
        return $this->json($access);
    }

    /**
     * @return JsonResponse
     */
    public function options()
    {
        $validator = Validator::make($this->getParam(), [
            'page_code' => [
                'required',
                Rule::exists(AdminPage::class, 'code')
                    ->withoutTrashed(),
            ],
        ], [
            'page_code.required' => '页面标识不能为空',
            'page_code.exists' => '页面标识不存在',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $pageId = AdminPageIdGet::init()
            ->setCode($this->getParam('page_code'))
            ->run()
            ->getId();
        if (empty($pageId)) {
            return $this->success();
        }
        $adminPageOptions = AdminPageOption::query()
            ->select('id')
            ->where('admin_page_id', $pageId)
            ->get();
        foreach ($adminPageOptions as $adminPageOption) {
            AdminPageOptionDelete::init()
                ->setId(Arr::get($adminPageOption, 'id'))
                ->run();
        }
        $options = $this->getParam('options');
        if (empty($options)) {
            return $this->success();
        }
        $pageOptionIds = [];
        foreach ($options as $option) {
            $pageOptionId = AdminPageOptionAdd::init()
                ->setAdminPageId($pageId)
                ->setCode(Arr::get($option, 'code'))
                ->setName(Arr::get($option, 'name'))
                ->run()
                ->getId();
            if ($pageOptionId) {
                $pageOptionIds = Arr::prepend($pageOptionIds, $pageOptionId);
            }
        }
        $model = AdminPageOption::query()
            ->whereIn('id', $pageOptionIds);
        if (!$this->isAdministrator()) {
            $adminPageOptionIds = AdminPageOptionIdsByAdminUserIdGet::init()
                ->setAdminUserId($this->getLoginAdminUserId())
                ->run()
                ->getAdminPageOptionIds();
            $model->whereIn('id', array_intersect($pageOptionIds, $adminPageOptionIds));
        }
        $dataIndexes = $model
            ->pluck('code');
        return $this->json(compact('dataIndexes'));
    }

    public function menu()
    {
        $model = AdminMenu::query()
            ->where('parent_id', 0);
        $administrator = $this->isAdministrator();
        $adminMenuIds = [];
        $menus = [];
        if (empty($administrator)) {
            $adminMenuIds = (array) AdminMenuIdsByAdminUserIdGet::init()
                ->setAdminUserId($this->getLoginAdminUserId())
                ->run()
                ->getAdminMenuIds();
        }
        $model = $model->orderByDesc('sort')
            ->get();
        $model->load([
            'children',
        ]);
        foreach ($model as $value) {
            if ($menu = $this->menuItem($value, $administrator, $adminMenuIds)) {
                $menus[] = $menu;
            }
        }
        return $this->json($menus);
    }

    protected function menuItem($value, $administrator, $adminMenuIds)
    {
        if (empty($administrator) && !in_array(Arr::get($value, 'id'), $adminMenuIds)) {
            return [];
        }
        $route = Arr::get($value, 'config');
        $route = Arr::add($route, 'name', Arr::get($value, 'name'));
        $route = Arr::add($route, 'path', Arr::get($value, 'path'));
        if (Arr::get($value, 'children')) {
            $routes = [];
            $children = Arr::get($value, 'children');
            foreach ($children as $child) {
                if ($itemRoute = $this->menuItem($child, $administrator, $adminMenuIds)) {
                    $routes[] = $itemRoute;
                }
            }
            if (!empty($routes)) {
                Arr::set($route, 'routes', $routes);
            }
        }
        return $route;
    }
}
