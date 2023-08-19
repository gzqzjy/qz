<?php

namespace Qz\Http\Controllers\Admin\Tag;

use Qz\Cores\Tag\TagAdd;
use Qz\Cores\Tag\TagBatchGroup;
use Qz\Cores\Tag\TagDelete;
use Qz\Cores\Tag\TagUpdate;
use Qz\Http\Controllers\Admin\AdminController;
use Qz\Models\Tag;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class TagController extends AdminController
{
    public function get()
    {
        $model = Tag::query();
        $model = $this->filter($model);
        $model->whereIn('admin_user_id', $this->getAccessAdminUserIds());
        $model = $model->latest()
            ->paginate($this->getParam('page_size'));
        call_user_func([$model, 'load'], [
            'tagGroupTags.tagGroup'
        ]);
        foreach ($model->items() as &$item) {
            $tagGroupName = '';
            if (Arr::get($item, 'tagGroupTags')->isNotEmpty()) {
                $tagGroups = Arr::get($item, 'tagGroupTags');
                $tagGroupName = implode("、", Arr::pluck(Arr::pluck($tagGroups, 'tagGroup'), 'name'));
                $item->tag_group_ids = Arr::pluck(Arr::pluck($tagGroups, 'tagGroup'), 'id');
            }
            $item->tag_group_name = $tagGroupName;

            Arr::forget($item, 'tagGroupTags');
        }
        $data = [
            'data' => $model->items(),
            'total' => $model->total(),
            'page_size' => $model->perPage(),
            'current' => $model->currentPage(),
        ];
        return $this->json($data);
    }

    public function all()
    {
        $model = Tag::query()
            ->where('status', Tag::STATUS_ENABLE);
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
        $this->paramValidate([
            'name' => [
                'required',
                Rule::unique(Tag::class)
                    ->where('name', $this->getParam('name'))
                    ->where('customer_id', $this->getCustomerId())
                    ->withoutTrashed(),
            ],
        ], [
            'name.required' => '标签名称是必须的',
            'name.unique' => '已存在重复标签名称',
        ]);
        $id = TagAdd::init()
            ->setName($this->getParam('name'))
            ->setStatus(Tag::STATUS_ENABLE)
            ->setCustomerId($this->getCustomerId())
            ->setAdminUserId($this->getLoginAdminUserId())
            ->setTagGroupIds($this->getParam('tag_group_ids'))
            ->run()
            ->getId();
        return $this->success(compact('id'));
    }

    public function update()
    {
        $this->paramValidate([
            'name' => [
                Rule::unique(Tag::class)
                    ->where('name', $this->getParam('name'))
                    ->where('customer_id', $this->getCustomerId())
                    ->ignore($this->getParam('id'))
                    ->withoutTrashed(),
            ],
        ], [
            'name.unique' => '已存在重复标签名称',
        ]);
        $id = TagUpdate::init()
            ->setId($this->getParam('id'))
            ->setName($this->getParam('name'))
            ->setStatus($this->getParam('status'))
            ->setTagGroupIds($this->getParam('tag_group_ids'))
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
                TagDelete::init()
                    ->setId($id)
                    ->run();
            }
        } else {
            TagDelete::init()
                ->setId(Arr::get($param, 'id'))
                ->run();
        }
        return $this->success([]);
    }

    public function batchGroup()
    {
        TagBatchGroup::init()
            ->setId($this->getParam('id'))
            ->setTagGroupId($this->getParam('tag_group_id'))
            ->run();
        return $this->success([]);
    }
}
