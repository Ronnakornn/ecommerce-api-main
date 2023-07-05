<?php

namespace App\Repositories\Eloquent\Criteria\Stock;

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

            if (key_exists('lot', $filters)) {
                $model = $model->where('lot', 'like', '%' . Arr::get($filters, 'lot', '') . '%');
            }

            if (key_exists('cost', $filters)) {
                $model = $model->where('cost', 'like', '%' . Arr::get($filters, 'cost', '') . '%');
            }

            if (key_exists('description', $filters)) {
                $model = $model->where('description', 'like', '%' . Arr::get($filters, 'description', '') . '%');
            }

            if (key_exists('keyword', $filters)) {
                $model = $model->where('lot', 'like', '%' . Arr::get($filters, 'keyword', '') . '%');
                $model = $model->orWhere('cost', 'like', '%' . Arr::get($filters, 'keyword', '') . '%');
                $model = $model->orWhere('description', 'like', '%' . Arr::get($filters, 'keyword', '') . '%');
            }
        }

        return $model;
    }
}
