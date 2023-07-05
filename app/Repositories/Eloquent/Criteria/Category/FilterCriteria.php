<?php

namespace App\Repositories\Eloquent\Criteria\Category;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class FilterCriteria implements CriteriaInterface
{
    /**
     * @param Builder $model
     * @param RepositoryInterface $repository
     * @return Builder
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $filters = json_decode(request()->get('filters'), 1);

        if (is_array($filters)) {
            if (key_exists('name', $filters)) {
                $model = $model->where('name', 'like', '%' . Arr::get($filters, 'name', '') . '%');
            }

            if (key_exists('description', $filters)) {
                $model = $model->where('description', 'like', '%' . Arr::get($filters, 'description', '') . '%');
            }
        }

        return $model;
    }
}
