<?php

namespace App\Http\Controllers\API\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiBaseController;
use App\Http\Resources\UserResource;
use App\Repositories\Interfaces\UserRepository;
use App\Repositories\Exceptions\RepositoryException;

class UserController extends ApiBaseController
{
    protected $userRepository;
    protected $resource = UserResource::class;

    public function  __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $users = $this->userRepository->take($request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse($users);
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
            $user = $this->userRepository->findById($id);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse($user);
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
            $new_user = $this->userRepository->store($request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse($new_user);
    }

    /**
     * Update
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        try {
            $user = $this->userRepository->modify($id, $request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse($user);
    }

    /**
     * Destroy
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->userRepository->destroy($id);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse([], null, null, false, "Delete user success");
    }
}
