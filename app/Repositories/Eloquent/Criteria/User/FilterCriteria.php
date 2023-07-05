<?php

namespace App\Repositories\Eloquent\Criteria\User;

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
     * params: filters={"name":"...", "email":"...", "status":"...", "user_role":"...", "keyword":"..."}
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $filters = json_decode(request()->get('filters'), 1);

        if (is_array($filters)) {
            if (key_exists('name', $filters)) {
                $model = $model->where('name', 'like', '%' . Arr::get($filters, 'name', '') . '%');
            }
            if (key_exists('email', $filters)) {
                $model = $model->where('email', 'like', '%' . Arr::get($filters, 'email', '') . '%');
            }
            if (key_exists('status', $filters)) {
                $model = $model->where('status', Arr::get($filters, 'status'));
            }
            if (key_exists('user_role', $filters)) {
                $model = $model->where('user_role', Arr::get($filters, 'user_role'));
            }
            if (key_exists('keyword', $filters)) {

                $model = $model->where('name', 'like', '%' . Arr::get($filters, 'keyword', '') . '%');
                $model = $model->orWhere('email', 'like', '%' . Arr::get($filters, 'keyword', '') . '%');
                $model = $model->orWhere('user_info', 'like', '%' . Arr::get($filters, 'keyword', '') . '%');

                if (key_exists('status', $filters)) {
                    $model = $model->where('status', Arr::get($filters, 'status'));
                }
            }
        }

        return $model;
    }
}
