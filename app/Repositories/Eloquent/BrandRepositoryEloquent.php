<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Repositories\Exceptions\RepositoryException;
use App\Repositories\Interfaces\BrandRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;
use App\Repositories\Eloquent\BaseRepositoryEloquent;
use App\Repositories\Eloquent\Criteria\Brand\FilterCriteria;
use App\Repositories\Eloquent\Criteria\Brand\SortCriteria;
use App\Repositories\Validators\Brand\BrandStoreValidation;
use App\Repositories\Validators\Brand\BrandModifyValidation;

class BrandRepositoryEloquent extends BaseRepositoryEloquent implements BrandRepository
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
        ]);

        return $parameters;
    }

    /**
     * @param array $parameters
     * @return string \App\Repositories\Validators\BaseValidator
     */
    protected function storeValidator(array $parameters)
    {
        return BrandStoreValidation::make($parameters);
    }

    /**
     * @param array $parameters
     * @param Brand $brand
     * @return array
     */
    protected function modifyValidator(array $parameters)
    {
        return BrandModifyValidation::make($parameters);
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
        return Brand::class;
    }

    /**
     * Function for listing bands
     *
     * @param array $parameters
     * @return \App\Models\Brand
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
     * Function for get brand by id
     *
     * @param $id
     * @return \App\Models\Brand
     * @throws \App\Repositories\Exceptions\RepositoryException : 500005 => "BRAND_NOT_FOUND"
     *
     */
    public function findById($id)
    {
        $brand = $this->model->find($id);
        if (empty($brand)) {
            throw new RepositoryException(500005);
        }
        return $brand;
    }

    /**
     * Function for store new brand
     *
     * @param array $parameters
     * @return \App\Models\Brand
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 500002 => "BRAND_CREATE_VALIDATE_ERROR"
     * 500004 => "BRAND_CREATE_ERROR"
     *
     */
    function store(array $parameters)
    {
        /** @var Validator $validator */
        $validator = $this->storeValidator($parameters);

        if ($validator->fails()) {
            throw new RepositoryException(500002, $validator->errors()->toArray());
        }

        try {
            $brand = DB::transaction(function () use ($parameters) {
                $insert = $this->prepareData($parameters);
                $brand = $this->query()->create($insert);
                return $brand;
            });
        } catch (\Exception $e) {
            throw new RepositoryException(500004, [$e->getMessage()]);
        }

        return $brand;
    }

    /**
     * Function for modify brand {id}
     *
     * @param $id
     * @param array $parameters
     * @return \App\Models\brand
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 500001 => "BRAND_NOT_FOUND"
     * 500003 => "BRAND_UPDATE_VALIDATE_ERROR"
     * 500005 => "Brand_UPDATE_ERROR"
     *
     */
    public function modify($id, array $parameters = [])
    {
        $brand = $this->model->find($id);
        if (empty($brand)) {
            throw new RepositoryException(500001);
        }

        /** @var Validator $validator */
        $validator = $this->modifyValidator($parameters);

        if ($validator->fails()) {
            throw new RepositoryException(500003, $validator->errors()->toArray());
        }

        try {
            $brand = DB::transaction(function () use ($brand, $parameters) {
                $data = $this->prepareData($parameters);
                $brand->update($data);
                return $brand;
            });
        } catch (\Exception $exception) {
            throw new RepositoryException(500005, [$exception->getMessage()]);
        }

        return $brand->fresh();
    }

    /**
     * Function for destroy brand {id}
     *
     * @param $id
     * @return boolean|null
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 500000 => "BRAND_ERROR"
     * 500006 => "BRAND_DELETE_ERROR"
     *
     */
    public function destroy($id)
    {
        $brand = $this->model->find($id);
        if (empty($brand)) {
            throw new RepositoryException(500000);
        }

        try {
            return $brand->delete();
        } catch (\Exception $exception) {
            throw new RepositoryException(500006);
        }
    }
}
