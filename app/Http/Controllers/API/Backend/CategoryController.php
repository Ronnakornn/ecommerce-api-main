<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Controllers\API\ApiBaseController;
use App\Repositories\Exceptions\RepositoryException;
use App\Repositories\Interfaces\CategoryRepository;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class CategoryController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    protected $resource = CategoryResource::class;
    protected $categoryRepository;

    public function  __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index(Request $request)
    {
        try {
            $params = [
                'per_page' => $request->input('per_page', 30),
            ];
            $categories = $this->categoryRepository->take($params);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $category = $this->categoryRepository->store($request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($category);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $category = $this->categoryRepository->findById($id);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = $this->categoryRepository->modify($id, $request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        try {
            $category = $this->categoryRepository->destroy($id);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }

        return $this->successResponse([], null, null, false, 'Delete category success');
    }
}
