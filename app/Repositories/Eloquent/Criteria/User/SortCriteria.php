<?php

namespace App\Repositories\Eloquent\Criteria\User;

use App\Repositories\Eloquent\Criteria\BaseSortCriteria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class SortCriteria extends BaseSortCriteria implements CriteriaInterface
{
    /**
     * @param Builder $model
     * @param RepositoryInterface $repository
     * @return Builder
     * params: sorted={"column":"...", "order":"..."}
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $sorted = json_decode(request()->get('sorted'), 1);
        $model = parent::apply($model, $repository);
        return $model->orderBy(Arr::get($sorted, 'column', 'created_at'), Arr::get($sorted, 'order', 'desc'));
    }
}
