<?php

namespace App\Repositories\Eloquent;

use App\Imports\ProductsImport;
use App\Models\Product;
use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Repositories\Eloquent\BaseRepositoryEloquent;
use App\Repositories\Eloquent\Criteria\Product\FilterCriteria;
use App\Repositories\Eloquent\Criteria\Product\SortCriteria;
use App\Repositories\Interfaces\ProductRepository;
use App\Repositories\Validators\Product\ProductModifyValidation;
use App\Repositories\Validators\Product\ProductStoreFileValidation;
use App\Repositories\Validators\Product\ProductStoreValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;

class ProductRepositoryEloquent extends BaseRepositoryEloquent implements ProductRepository
{
    /**
     * @param array $parameters
     * @return string \App\Repositories\Product\ProductStoreValidation
     */
    protected function storeValidator(array $parameters)
    {
        return ProductStoreValidation::make($parameters);
    }

    /**
     * @param array $parameters
     * @param Product $product
     * @return string \App\Repositories\Validators\Product\ProductModifyValidation
     */
    protected function modifyValidator(array $parameters, Product $product)
    {
        $validator = new ProductModifyValidation($parameters, $product);
        return $validator->createDefaultValidator();
    }

    /**
     * @param array $parameters
     * @param Product $product
     * @return string \App\Repositories\Validators\Product\ProductModifyValidation
     */
    protected function storeFileValidator(array $parameters)
    {
        return ProductStoreFileValidation::make($parameters);
    }

    /**
     * @param array $parameters
     * @return array
     */
    protected function prepareData(array $parameters)
    {
        $parameters = [
            'name' => Arr::get($parameters, 'name'),
            'description' => Arr::get($parameters, 'description'),
            'brand_id' => Arr::get($parameters, 'brand_id'),
            'category_id' => Arr::get($parameters, 'category_id'),
            'product_attr' => Arr::get($parameters, 'product_attr'),
            'amount' => Arr::get($parameters, 'amount'),
            'warranty' => Arr::get($parameters, 'warranty'),
            'remark' => Arr::get($parameters, 'remark'),
            'type' => Arr::get($parameters, 'type'),
            'status' => Arr::get($parameters, 'status')
        ];

        return $parameters;
    }

    /**
     * @param array $parameters
     * @param Product $product
     * @return array
     */
    protected function prepareModifyData(array $parameters, Product $product)
    {
        $parameters = [
            'name' => Arr::get($parameters, 'name', $product->name),
            'description' => Arr::get($parameters, 'description', $product->description),
            'brand_id' => Arr::get($parameters, 'brand_id', $product->brand_id),
            'category_id' => Arr::get($parameters, 'category_id', $product->category_id),
            'product_attr' => Arr::get($parameters, 'product_attr', $product->product_attr),
            'amount' => Arr::get($parameters, 'amount', $product->amount),
            'warranty' => Arr::get($parameters, 'warranty', $product->warranty),
            'remark' => Arr::get($parameters, 'remark', $product->remark),
            'type' => Arr::get($parameters, 'type', $product->type),
            'status' => Arr::get($parameters, 'status', $product->status)
        ];

        return $parameters;
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
        return Product::class;
    }

    /**
     * @param array $parameters
     * @return $product
     */
    public function take(array $parameters = [])
    {
        $products = Cache::rememberForever("products-take", function () use ($parameters) {
            if (! Arr::get($parameters, "per_page")) {
                return $this->get();
            }

            $perPage = Arr::get($parameters, 'per_page', 30);
            return $this->paginate($perPage);
        });

        return $products;
    }

    /**
     * @param array $parameters
     * @return $product
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 200006 => PRODUCT_CREATE_VALIDATE_ERROR
     * 200007 => PRODUCT_CREATE_ERROR
     */
    public function store(array $parameters = [])
    {
        /** @var Validator $validator */
        $validator = $this->storeValidator($parameters);

        if ($validator->fails()) {
            throw new RepositoryException(200006, $validator->errors()->toArray());
        }

        try {
            $product = DB::transaction(function () use ($parameters) {
                $insert = $this->prepareData($parameters);
                return $this->query()->create($insert);
            });
        } catch (\Exception $e) {
            throw new RepositoryException(200007, [$e->getMessage()]);
        }

        $this->flushCacheProduct($product);
        return $product;
    }

    /**

     * @return $product
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 200007 => PRODUCT_CREATE_ERROR
     */
    public function storeExcelFile($parameters)
    {   
        try {
            $product = Excel::queueImport(new ProductsImport, $parameters->file('uploaded_file')); 
        } catch (\Exception $e) {
            throw new RepositoryException(200007, [$e->getMessage()]);
        }
        return $product;
    }

    /**
     * @param $id, array $parameters
     * @return $product
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 200008 => PRODUCT_UPDATE_VALIDATE_ERROR
     * 200009 => PRODUCT_UPDATE_ERROR
     */
    public function modify($id, array $parameters = [])
    {
        $product = $this->find($id);
        /** @var Validator $validator */
        $validator = $this->modifyValidator($parameters, $product);

        if ($validator->fails()) {
            throw new RepositoryException(200008, $validator->errors()->toArray());
        }

        try {
            $product = DB::transaction(function () use ($product, $parameters) {
                $update = $this->prepareModifyData($parameters, $product);
                return $product->update($update);
            });
        } catch (\Exception $e) {
            throw new RepositoryException(200009, [$e->getMessage()]);
        }

        $this->flushCacheProduct($product);
        return $product->fresh();
    }

    /**
     * @param $id
     * @return $product
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 200005 => "PRODUCT_NOT_FOUND"
     */
    public function findById($id)
    {
        $product = Cache::rememberForever("products-findById-{$id}", function () use ($id) {
            return $this->model->find($id);
        });

        if (empty($product)) {
            throw new RepositoryException(200005, ["Product not found."]);
        }

        return $product;
    }

    /**
     * @param $id
     * @return boolean|null
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 200005 => PRODUCT_NOT_FOUND
     * 200010 => PRODUCT_DELETE_ERROR
     */
    public function destroy($id)
    {
        $product = $this->model->find($id);
        if(empty($product)){
            throw new RepositoryException(200005, ["Product not found."]);
        }

        try {
            $this->flushCacheProduct($product);
            return $product->delete();
        } catch (\Exception $exception) {
            throw new RepositoryException(200010, [$exception->getMessage()]);
        }
    }

    /**
     * Flush cache product
     */
    private function flushCacheProduct($product)
    {
        Cache::forget("products-take");
        Cache::forget("products-findById-{$product->id}");
    }
}
