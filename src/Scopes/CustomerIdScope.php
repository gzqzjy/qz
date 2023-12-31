<?php

namespace Qz\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Qz\Facades\Access;

class CustomerIdScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (Access::getCustomerId()) {
            $builder->where('customer_id', Access::getCustomerId());
        }
    }
}
