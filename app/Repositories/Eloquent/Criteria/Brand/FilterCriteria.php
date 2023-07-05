<?php

namespace App\Repositories\Eloquent\Criteria\Brand;

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
            if (key_exists('product', $filters)) {
                $model = $model->whereHas('products', function($query) use($filters){
                    $query->where('name', 'like', '%' . Arr::get($filters, 'product') . '%');
                });
            }
            if (key_exists('status', $filters)) {
                $model = $model->where('status', Arr::get($filters, 'status'));
            }
            if (key_exists('keyword', $filters)) {
                $model = $model->where('name', 'like', '%' . Arr::get($filters, 'keyword', '') . '%');
                $model = $model->orWhere('description', 'like', '%'. Arr::get($filters, 'keyword', '') . '%');
                $model = $model->orWhereHas('products', function($query) use($filters){
                    $query->where('name', 'like', '%'. Arr::get($filters, 'keyword', '') . '%');
                });
            
                if (key_exists('status', $filters)) {
                    $model = $model->where('status', Arr::get($filters, 'status'));
                }
            }
        }
        
        return $model;
    }
}
