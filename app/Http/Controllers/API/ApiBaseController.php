<?php

namespace App\Http\Controllers\API;

use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller as Controller;

class ApiBaseController extends Controller
{
    /** @var \Illuminate\Http\Resources\Json\JsonResource|null  */
    protected $resource = null;
    protected function setResource(string $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null $collection
     * @param string|null $resource
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\Resources\Json\JsonResource|array
     */
    protected function successResponse(
        $collection = null,
        string $resource = null,
        $custom_data = null,
        $force_custom_data = false,
        $message = "OK",
    ) {
        $successResponse = [
            'status' => [
                'code' => 200,
                'message' => $message,
            ],
        ];

        if (!empty($custom_data)) {
            $successResponse['custom_data'] = $custom_data;
        }

        if (empty($collection)) {
            return $successResponse;
        } elseif ($force_custom_data) {
            $successResponse['data'] = $collection;

            return $successResponse;
        }

        if (!empty($resource)) {
            $this->setResource($resource);
        }

        if (empty($this->resource)) {
            return $collection;
        }

        if ($collection instanceof LengthAwarePaginator || $collection instanceof Collection) {
            $jsonResource = $this->resource::collection($collection);
        } else {
            $jsonResource = $this->resource::make($collection);
        }

        return $jsonResource->additional($successResponse);
    }

    protected function errorResponse(RepositoryException $exception)
    {
        $statusCode = $exception->getCode();
        $errorCode = $exception->getErrorCode();
        $errorMessage = $exception->getErrorMessage();
        $errors = $exception->getErrors();
        $statusMessage = Arr::get(Response::$statusTexts, $statusCode);

        if (empty($statusMessage)) {
            $statusCode = 422;
            $statusMessage = Arr::get(Response::$statusTexts, $statusCode);
        }

        return response()->json([
            'status' => [
                'code' => $statusCode,
                'message' => $statusMessage,
            ],
            'error' => [
                'code' => $errorCode,
                'message' => $errorMessage,
                'errors' => $errors,
            ],
        ]);
    }
}

