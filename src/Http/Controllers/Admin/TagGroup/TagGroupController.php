<?php

namespace Qz\Http\Controllers\Admin\TagGroup;

use Qz\Cores\TagGroup\TagGroupAdd;
use Qz\Cores\TagGroup\TagGroupDelete;
use Qz\Cores\TagGroup\TagGroupUpdate;
use Qz\Http\Controllers\Admin\AdminController;
use Qz\Models\TagGroup;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class TagGroupController extends AdminController
{
    public function get()
    {
        $model = TagGroup::query();
        $model = $this->filter($model);
        $model->whereIn('admin_user_id', $this->getAccessAdminUserIds());
        $model = $model->latest()
            ->paginate($this->getParam('page_size'));
        $data = $model->items();
        $data = [
            'data' => $data,
            'total' => $model->total(),
            'page_size' => $model->perPage(),
            'current' => $model->currentPage(),
        ];
        return $this->json($data);
    }

    public function all()
    {
        $model = TagGroup::query()
            ->where('status', TagGroup::STATUS_ENABLE);
        $model = $this->filter($model);
        $model->whereIn('admin_user_id', $this->getAccessAdminUserIds());
        if ($this->getParam('select')) {
            $model->selectRaw($this->getParam('select'));
        }
        $model = $model->get();

        return $this->response($model);
    }

    public function store()
    {
        $validator = Validator::make($this->getParam(), [
            'name' => [
                'required',
                Rule::unique(TagGroup::class)
                    ->where('name', $this->getParam('name'))
                    ->where('customer_id', $this->getCustomerId())
                    ->withoutTrashed(),
            ],
        ], [
            'name.required' => '标签名称是必须的',
            'name.unique' => '已存在重复标签名称',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $id = TagGroupAdd::init()
            ->setName($this->getParam('name'))
            ->setStatus(TagGroup::STATUS_ENABLE)
            ->setCustomerId($this->getCustomerId())
            ->setAdminUserId($this->getLoginAdminUserId())
            ->run()
            ->getId();
        return $this->success(compact('id'));
    }

    public function update()
    {
        $validator = Validator::make($this->getParam(), [
            'name' => [
                Rule::unique(TagGroup::class)
                    ->where('name', $this->getParam('name'))
                    ->where('customer_id', $this->getCustomerId())
                    ->ignore($this->getParam('id'))
                    ->withoutTrashed(),
            ],
        ], [
            'name.unique' => '已存在重复标签名称',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $id = TagGroupUpdate::init()
            ->setId($this->getParam('id'))
            ->setName($this->getParam('name'))
            ->setStatus($this->getParam('status'))
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
                TagGroupDelete::init()
                    ->setId($id)
                    ->run();
            }
        } else {
            TagGroupDelete::init()
                ->setId(Arr::get($param, 'id'))
                ->run();
        }
        return $this->success([]);
    }
}
