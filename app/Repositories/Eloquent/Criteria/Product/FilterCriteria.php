<?php

namespace App\Repositories\Eloquent\Criteria\Product;

use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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

            if (key_exists('type', $filters)) {
                $model = $model->where('type', Arr::get($filters, 'type'));
            }

            if (key_exists('status', $filters)) {
                $model = $model->where('status', Arr::get($filters, 'status'));
            }

            if (key_exists('category', $filters)) {
                $model = $model->whereHas('category', function($query) use($filters){
                    $query->where('name', 'like', '%' . Arr::get($filters, 'category') . '%');
                });
            }

            if (key_exists('brand', $filters)) {
                $model = $model->whereHas('brand', function($query) use($filters){
                    $query->where('name', 'like', '%' . Arr::get($filters, 'brand') . '%');
                });
            }

            if (key_exists('warranty', $filters)) {
                $model = $model->where('warranty', Arr::get($filters, 'warranty'));
            }

            if(key_exists('sku', $filters)){
                $model = $model->where('product_attr->sku', 'like', '%' . Arr::get($filters, 'sku') . '%');
            }

            if(key_exists('price_min', $filters) && key_exists('price_max', $filters)){
                $model = $model->whereBetween('product_attr->price', [Arr::get($filters, 'price_min'), Arr::get($filters, 'price_max')]);
            }

            if (key_exists('keyword', $filters)) {
                $model = $model->where('name', 'like', '%' . Arr::get($filters, 'keyword', '') . '%');
                $model = $model->orWhere('type', 'like', '%' . Arr::get($filters, 'keyword', '') . '%');
                $model = $model->orWhere('type', 'like', '%'. Arr::get($filters, 'keyword', '') . '%');
                $model = $model->orWhere('status', 'like', '%'. Arr::get($filters, 'keyword', '') . '%');
                $model = $model->orWhere('type', 'like', '%'. Arr::get($filters, 'keyword', '') . '%');
                $model = $model->orWhereHas('category', function($query) use($filters){
                    $query->where('name', 'like', '%'. Arr::get($filters, 'keyword', '') . '%');
                });
                $model = $model->orWhereHas('brand', function($query) use($filters){
                    $query->where('name', 'like', '%'. Arr::get($filters, 'keyword', '') . '%');
                });
                $model = $model->orWhere('warranty', 'like', '%'. Arr::get($filters, 'keyword', '') . '%');

                if (key_exists('status', $filters)) {
                    $model = $model->where('status', Arr::get($filters, 'status'));
                }
            }
        }

        return $model;
    }
}
