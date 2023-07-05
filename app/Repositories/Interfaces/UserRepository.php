<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepository
{
    /**
     * @param array $parameters
     * @return \App\Models\User
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function take(array $parameters = []);

    /**
     * @param array $parameters
     * @return \App\Models\User
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function store(array $parameters);

    /**
     * @param $id
     * @return \App\Models\User
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function findById($id);

    /**
     * @param $id
     * @param array $parameters
     * @return \App\Models\User
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function modify($id, array $parameters);

    /**
     * @param $id
     * @return boolean|null
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function destroy($id);

    /**
     * @param array $parameters
     * @return \App\Models\User
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function login(array $parameters);

    /**
     * @param \App\Models\User
     * @return boolean|null
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function logout(User $user);

    /**
     * @param array $parameters
     * @return boolean|null
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function generateKey(array $parameters);
}
