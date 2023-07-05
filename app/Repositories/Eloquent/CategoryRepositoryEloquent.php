<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Exceptions\RepositoryException;
use App\Repositories\Interfaces\CategoryRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;
use App\Repositories\Eloquent\BaseRepositoryEloquent;
use App\Repositories\Eloquent\Criteria\Category\FilterCriteria;
use App\Repositories\Eloquent\Criteria\Category\SortCriteria;
use App\Repositories\Validators\Category\CategoryStoreValidation;
use App\Repositories\Validators\Category\CategoryModifyValidation;

class CategoryRepositoryEloquent extends BaseRepositoryEloquent implements CategoryRepository
{

    /**
     * @param array $parameters
     * @return array
     */
    protected function prepareData(array $parameters): array
    {
        $parameters = Arr::only($parameters, [
            'name',
            'description',
            'status'
        ]);

        return $parameters;
    }

    /**
     * @param array $parameters
     * @return string \App\Repositories\Validators\BaseValidator
     */
    protected function storeValidator(array $parameters)
    {
        return CategoryStoreValidation::make($parameters);
    }

    /**
     * @param array $parameters
     * @param Category $category
     * @return array
     */
    protected function modifyValidator(array $parameters)
    {
        return CategoryModifyValidation::make($parameters);
    }

    /**
     * @return Builder
     */
    protected function query()
    {
        $this->applyCriteria();
        $this->applyScope();
        $query = $this->model->newQuery();
        $this->resetCriteria();
        $this->resetScope();
        return $query;
    }
    
    /**
     * Fuction for searching and sorting
     *
     * @return Builder
     */
    public function boot()
    {
        parent::boot();
        $this->pushCriteria(FilterCriteria::class);
        $this->pushCriteria(SortCriteria::class);
    }

    /**
     * @return array
     */
    public function model()
    {
        return Category::class;
    }

    /**
     * Function for listing categories
     *
     * @param array $parameters
     * @return \App\Models\Category
     *
     */
    public function take(array $parameters = [])
    {
        if (empty($parameters['per_page'])) {
            return $this->get();
        }
        $perPage = Arr::get($parameters, 'per_page', 30);
        return $this->paginate($perPage);
    }

    /**
     * Function for get category by id
     *
     * @param $id
     * @return \App\Models\Category
     * @throws \App\Repositories\Exceptions\RepositoryException : 400005 => "CATEGORY_NOT_FOUND"
     *
     */
    public function findById($id)
    {
        $category = $this->model->find($id);
        if (empty($category)) {
            throw new RepositoryException(400005);
        }
        return $category;
    }

    /**
     * Function for store new category
     *
     * @param array $parameters
     * @return \App\Models\Category
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 400002 => "CATEGORY_CREATE_VALIDATE_ERROR"
     * 400004 => "CATEGORY_CREATE_ERROR"
     *
     */
    function store(array $parameters)
    {
        /** @var Validator $validator */
        $validator = $this->storeValidator($parameters);

        if ($validator->fails()) {
            throw new RepositoryException(400002, $validator->errors()->toArray());
        }

        try {
            $category = DB::transaction(function () use ($parameters) {
                $insert = $this->prepareData($parameters);
                $category = $this->query()->create($insert);
                return $category;
            });
        } catch (\Exception $e) {
            throw new RepositoryException(400004, [$e->getMessage()]);
        }

        return $category;
    }

    /**
     * Function for modify category {id}
     *
     * @param $id
     * @param array $parameters
     * @return \App\Models\Category
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 400001 => "CATEGORY_NOT_FOUND"
     * 400003 => "CATEGORY_UPDATE_VALIDATE_ERROR"
     * 400005 => "CATEGORY_UPDATE_ERROR"
     *
     */
    public function modify($id, array $parameters = [])
    {
        $category = $this->model->find($id);
        if (empty($category)) {
            throw new RepositoryException(400001);
        }

        /** @var Validator $validator */
        $validator = $this->modifyValidator($parameters);

        if ($validator->fails()) {
            throw new RepositoryException(400003, $validator->errors()->toArray());
        }

        try {
            $category = DB::transaction(function () use ($category, $parameters) {
                $data = $this->prepareData($parameters);
                $category->update($data);
                return $category;
            });
        } catch (\Exception $exception) {
            throw new RepositoryException(400005, [$exception->getMessage()]);
        }

        return $category->fresh();
    }

    /**
     * Function for destroy category {id}
     *
     * @param $id
     * @return boolean|null
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 400000 => "CATEGORY_ERROR"
     * 400006 => "CATEGORY_DELETE_ERROR"
     *
     */
    public function destroy($id)
    {
        $category = $this->model->find($id);
        if (empty($category)) {
            throw new RepositoryException(400006);
        }

        try {
            return $category->delete();
        } catch (\Exception $exception) {
            throw new RepositoryException(400000);
        }
    }
}
