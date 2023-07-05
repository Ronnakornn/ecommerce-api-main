<?php

namespace App\Http\Controllers\API\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiBaseController;
use App\Http\Resources\StockResource;
use App\Repositories\Exceptions\RepositoryException;
use App\Repositories\Interfaces\StockRepository;

class StockController extends ApiBaseController
{
    protected $stockRepository;
    protected $resource = StockResource::class;

    public function  __construct(StockRepository $stockRepository)
    {
        $this->stockRepository = $stockRepository;
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request)
    {
        try {
            $stocks = $this->stockRepository->take($request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse($stocks);
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store(Request $request)
    {
        try {
            $new_stock = $this->stockRepository->store($request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse($new_stock);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $stock = $this->stockRepository->findById($id);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse($stock);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        try {
            $stock = $this->stockRepository->modify($id, $request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse($stock);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->stockRepository->destroy($id);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse([], null, null, false, "Delete stock success");
    }
}
