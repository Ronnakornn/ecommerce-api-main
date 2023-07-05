<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\API\ApiBaseController;
use App\Http\Resources\BrandResource;
use App\Repositories\Exceptions\RepositoryException;
use App\Repositories\Interfaces\BrandRepository;
use Illuminate\Http\Request;

class BrandController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    protected $brandRepository;
    protected $resource = BrandResource::class;

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
}
