<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\API\ApiBaseController;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Imports\ProductsImport;
use App\Repositories\Eloquent\ProductRepositoryEloquent;
use App\Repositories\Exceptions\RepositoryException;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends ApiBaseController
{
    protected $productRepository;
    protected $resource = ProductResource::class;

    public function __construct(ProductRepositoryEloquent $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $params = [
                'per_page' => $request->input('per_page', 30),
                'filters' => $request->input('filters')
            ];
            $products = $this->productRepository->take($params);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($products);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $product = $this->productRepository->store($request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($product);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeExcel(Request $request)
    {
        try {
            $product = $this->productRepository->storeExcelFile($request);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse([]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $product = $this->productRepository->findById($id);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        try {
            $product = $this->productRepository->modify($id, $request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = $this->productRepository->destroy($id);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse([]);
    }
}
