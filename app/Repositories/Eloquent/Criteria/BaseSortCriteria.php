<?php

namespace App\Repositories\Eloquent\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class BaseSortCriteria implements CriteriaInterface
{
    /**
     * @param Builder $model
     * @param RepositoryInterface $repository
     * @return Builder
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $sort = request()->get('sort');
        if (is_null($sort)) {
            return $model;
        }
        $sorts = explode(',', $sort);
        foreach ($sorts as $sort) {
            $sort = explode(':', $sort);
            $column = reset($sort);
            $direction = end($sort);
            if (!in_array($direction, ['asc', 'desc'])) {
                $direction = 'asc';
            }
            $locale = app()->getLocale();
            if (
                in_array('Spatie\Translatable\HasTranslations', $this->classUsesDeep($model))
                && in_array($column, $model->translatable)
            ) {
                if ($locale === 'th') {
                    $model = $model->orderByRaw("CONVERT(JSON_EXTRACT({$column}, '$.{$locale}') USING TIS620) {$direction}");
                } else {
                    $model = $model->orderBy("{$column}->{$locale}", $direction);
                }
            } else {
                $columnType = DB::getSchemaBuilder()->getColumnType($model->getModel()->getTable(), $column);
                if (
                    $locale === 'th'
                    && in_array($columnType, ['string', 'text', 'longtext'])
                ) {
                    $model = $model->orderByRaw("CONVERT(`{$column}` USING TIS620) {$direction}");
                } else {
                    $model = $model->orderBy($column, $direction);
                }
            }
        }
        return $model;
    }
    private function classUsesDeep($class, $autoload = true)
    {
        $traits = [];
        // Get traits of all parent classes
        do {
            $traits = array_merge(class_uses($class, $autoload), $traits);
        } while ($class = get_parent_class($class));
        // Get traits of all parent traits
        $traitsToSearch = $traits;
        while (!empty($traitsToSearch)) {
            $newTraits = class_uses(array_pop($traitsToSearch), $autoload);
            $traits = array_merge($newTraits, $traits);
            $traitsToSearch = array_merge($newTraits, $traitsToSearch);
        };
        foreach ($traits as $trait => $same) {
            $traits = array_merge(class_uses($trait, $autoload), $traits);
        }
        return array_unique($traits);
    }
}
