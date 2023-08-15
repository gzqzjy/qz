<?php

namespace Qz\Http\Controllers\Admin;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Qz\Cores\AdminUser\AdminUserIdsByAdminUserIdGet;
use Qz\Cores\AdminUser\CategoryIdsByAdminUserIdGet;
use Qz\Cores\Common\Filter;
use Qz\Facades\Access;
use Qz\Http\Controllers\Controller;

class AdminController extends Controller
{
    final protected function page(LengthAwarePaginator $paginator)
    {
        $data = [
            'data' => $paginator->items(),
            'total' => $paginator->total(),
            'pageSize' => $paginator->perPage(),
            'current' => $paginator->currentPage(),
        ];
        return $this->json($this->camel($data));
    }

    final protected function getPageSize()
    {
        return min(1000, max(1, (int) $this->getParam('page_size', 20)));
    }

    protected function filter(Builder $model)
    {
        $model = Filter::init()
            ->setModel($model)
            ->setParam($this->getParam('filter'))
            ->run()
            ->getModel();
        $sort = $this->getParam('sort');
        if (!empty($sort) && is_array($sort)) {
            foreach ($sort as $key => $value) {
                if ($value == 'ascend') {
                    $model->orderBy($key);
                } elseif ($value == 'descend') {
                    $model->orderByDesc($key);
                }
            }
        }
        return $model;
    }

    protected function getChildFilter()
    {
        $filter = [];
        if ($this->getParam('filter')) {
            foreach ($this->getParam('filter') as $item) {
                $field = Str::snake(Arr::get($item, 'field'));
                if (strpos($field, '.') !== false) {
                    $firstField = Str::beforeLast($field, '.');
                    $otherField = Str::afterLast($field, '.');
                    $firstField = Str::camel($firstField);
                    $item['field'] = $otherField;
                    $filter[$firstField][] = $item;
                }
            }
        }
        return $filter;
    }

    protected function isAdministrator()
    {
        return Access::getAdministrator();
    }

    protected function getLoginAdminUserId()
    {
        return Access::getAdminUserId();
    }

    protected function getCustomerId()
    {
        return Access::getCustomerId();
    }

    protected function getLoginCategoryIdes()
    {
        return (array) CategoryIdsByAdminUserIdGet::init()
            ->setAdminUserId($this->getLoginAdminUserId())
            ->run()
            ->getIds();
    }

    protected function getAdminRequestId()
    {
        return Access::getAdminRequestId();
    }

    protected function getAccessAdminUserIds()
    {
        return  AdminUserIdsByAdminUserIdGet::init()
            ->setAdminUserId($this->getLoginAdminUserId())
            ->setAdminRequestId($this->getAdminRequestId())
            ->run()
            ->getIds();
    }
}
