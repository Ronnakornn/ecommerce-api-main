<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\API\ApiBaseController;
use App\Http\Resources\ProductResource;
use App\Repositories\Eloquent\ProductRepositoryEloquent;
use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Http\Request;

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
     * User Role => Customer, Company
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $products = $this->productRepository->take($request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($products);
    }

    /**
     * Display the specified resource.
     * User Role => Customer, Company
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

}
