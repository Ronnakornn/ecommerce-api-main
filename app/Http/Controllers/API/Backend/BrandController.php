<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\API\ApiBaseController;
use App\Repositories\Exceptions\RepositoryException;
use App\Repositories\Interfaces\BrandRepository;
use Illuminate\Http\Request;
use App\Http\Resources\BrandResource;

class BrandController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    protected $resource = BrandResource::class;
    protected $brandRepository;

    public function  __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function index(Request $request)
    {
        try {
            $params = [
                'per_page' => $request->input('per_page', 30),
            ];
            $brands = $this->brandRepository->take($params);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($brands);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $brand = $this->brandRepository->store($request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($brand);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $brand = $this->brandRepository->findById($id);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $brand = $this->brandRepository->modify($id, $request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($brand);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $brand = $this->brandRepository->destroy($id);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse([], null, null, false, 'Delete brand success');
    }
}
