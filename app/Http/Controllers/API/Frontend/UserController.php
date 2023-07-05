<?php

namespace App\Http\Controllers\API\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiBaseController;
use App\Http\Resources\UserResource;
use App\Repositories\Interfaces\UserRepository;
use App\Repositories\Exceptions\RepositoryException;
use Illuminate\Support\Facades\Auth;

class UserController extends ApiBaseController
{
    protected $userRepository;
    protected $resource = UserResource::class;

    public function  __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Show my profile
     *
     * @return \Illuminate\Http\Response
     *
     */
    public function showMyProfile()
    {
        try {
            $user = $this->userRepository->findById(Auth::user()->id);
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse($user);
    }

    /**
     * Update my profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     */
    public function updateMyProfile(Request $request)
    {
        try {
            $user = $this->userRepository->modify(Auth::user()->id, $request->all());
        } catch (RepositoryException $exception) {
            return $this->errorResponse($exception);
        }
        return $this->successResponse($user);
    }
}
