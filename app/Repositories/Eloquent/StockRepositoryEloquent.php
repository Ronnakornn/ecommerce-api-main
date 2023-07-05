<?php

namespace App\Repositories\Eloquent;

use App\Models\Stock;
use App\Repositories\Interfaces\StockRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use App\Repositories\Eloquent\BaseRepositoryEloquent;
use App\Repositories\Eloquent\Criteria\Stock\FilterCriteria;
use App\Repositories\Eloquent\Criteria\Stock\SortCriteria;
use App\Repositories\Exceptions\RepositoryException;
use App\Repositories\Validators\Stock\StockModifyValidation;
use App\Repositories\Validators\Stock\StockStoreValidation;
use Illuminate\Support\Facades\DB;

class StockRepositoryEloquent extends BaseRepositoryEloquent implements StockRepository
{
    /**
     * Function use validator for add new data.
     *
     * @param array $parameters
     * @return string \App\Repositories\Validators\BaseValidator
     */
    protected function storeValidator(array $parameters)
    {
        return StockStoreValidation::make($parameters);
    }

    /**
     * Function use validator for update data.
     *
     * @param array $parameters
     * @param Stock $stock
     * @return string \App\Repositories\Validators\BaseValidator
     */
    protected function modifyValidator(array $parameters, Stock $stock)
    {
        $validator = new StockModifyValidation($parameters, $stock);
        return $validator->createDefaultValidator();
    }

    /**
     * Function for prepare data before add data to database.
     *
     * @param array $parameters
     * @return array
     */
    protected function prepareData(array $parameters): array
    {
        $newStock = [
            "lot" => Arr::get($parameters, "lot"),
            "amount" => Arr::get($parameters, "amount"),
            "description" => Arr::get($parameters, "description"),
            "cost" => Arr::get($parameters, "cost"),
        ];

        return $newStock;
    }

    /**
     * Function for prepare data before update data to database.
     *
     * @param array $parameters
     * @param Stock $stock
     * @return array
     */
    protected function prepareModifyData(array $parameters, Stock $stock)
    {
        $update = [
            "lot" => Arr::get($parameters, "lot", $stock->lot),
            "amount" => Arr::get($parameters, "amount", $stock->amount),
            "description" => Arr::get($parameters, "description", $stock->description),
            "cost" => Arr::get($parameters, "cost", $stock->cost),
        ];

        return $update;
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
     * Fuction for use \App\Models\Stock
     *
     * @return \App\Models\Stock
     */
    public function model()
    {
        return Stock::class;
    }

    /**
     * Function for listing stocks
     *
     * @param array $parameters
     * @return \App\Models\Stock
     *
     */
    public function take(array $parameters = [])
    {
        if (!Arr::get($parameters, "per_page")) {
            return $this->get();
        }
        $stocks = Arr::get($parameters, "per_page", 30);
        return $this->paginate($stocks);
    }

    /**
     * Function for get stock by id
     *
     * @param $id
     * @return \App\Models\Stock
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 600005 => "STOCK_NOT_FOUND",
     *
     */
    public function findById($id)
    {
        $stock = $this->model->with(['products' => function ($product) {
            $product->select('name', 'status');
        }])->find($id);

        if (empty($stock)) {
            throw new RepositoryException(600005);
        }

        return $stock;
    }

    /**
     * Function for store new stock
     *
     * @param array $parameters
     * @return \App\Models\Stock
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 600006 => "STOCK_CREATE_VALIDATE_ERROR",
     * 600012 => "STOCK_CREATE_SAVE_DATA_ERROR",
     * 600013 => "STOCK_CREATE_SAVE_PIVOT_DATA_ERROR",
     * 600007 => "STOCK_CREATE_ERROR",
     *
     */
    public function store(array $parameters)
    {
        /** @var Validator $validator */
        $validator = $this->storeValidator($parameters);

        if ($validator->fails()) {
            throw new RepositoryException(600006, $validator->errors()->toArray());
        }

        try {
            $stock = DB::transaction(function () use ($parameters) {
                $stockInput = $this->prepareData($parameters);
                $dataStock = $this->query()->create($stockInput);
                if (!$dataStock) {
                    throw new RepositoryException(600012);
                }
                $products = json_decode(Arr::get($parameters, "product_ids"), 1);

                $dataStockProducts = $dataStock->products()->attach($products);
                if ($dataStockProducts === false) {
                    throw new RepositoryException(600013);
                }
                $dataStock["product_ids"] = $products;

                return $dataStock;
            });
        } catch (\Exception $e) {
            throw new RepositoryException(600007, [$e->getMessage()]);
        }

        return $stock;
    }

    /**
     * Function for modify stock by id
     *
     * @param $id
     * @param array $parameters
     * @return \App\Models\Stock
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 600008 => "STOCK_UPDATE_VALIDATE_ERROR",
     * 600014 => "STOCK_UPDATE_SAVE_DATA_ERROR",
     * 600015 => "STOCK_UPDATE_SAVE_PIVOT_DATA_ERROR",
     * 600009 => "STOCK_UPDATE_ERROR",
     *
     */
    public function modify($id, array $parameters)
    {
        $stock = $this->findById($id);
        /** @var Validator $validator */
        $validator = $this->modifyValidator($parameters, $stock);

        if ($validator->fails()) {
            throw new RepositoryException(600008, $validator->errors()->toArray());
        }

        try {
            $stock = DB::transaction(function () use ($parameters, $stock) {
                $dataUpdate = $this->prepareModifyData($parameters, $stock);
                $dataStock = $stock->update($dataUpdate);
                if (!$dataStock) {
                    throw new RepositoryException(600014);
                }

                $oldProductIds = $stock->products->map(function ($product) {
                    return $product->pivot->toArray();
                })->pluck('product_id')->toJson();

                $products = json_decode(Arr::get($parameters, "product_ids", $oldProductIds), 1);

                $dataStockProducts = $stock->products()->sync($products);
                if ($dataStockProducts === false) {
                    throw new RepositoryException(600015);
                }

                return $dataStock;
            });
        } catch (\Exception $e) {
            throw new RepositoryException(600009, [$e->getMessage()]);
        }

        return $this->findById($id);
    }

    /**
     * Function for delete stock by id
     *
     * @param $id
     * @return boolean|null
     * @throws \App\Repositories\Exceptions\RepositoryException
     * 600010 => "STOCK_DELETE_ERROR",
     *
     */
    public function destroy($id)
    {
        $stock = $this->findById($id);

        try {
            $stock->products()->detach();
            return $stock->delete();
        } catch (\Exception $e) {
            throw new RepositoryException(600010, [$e->getMessage()]);
        }
    }
}
