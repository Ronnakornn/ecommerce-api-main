<?php

namespace App\Repositories\Interfaces;

interface CategoryRepository
{
    /**
     * @param array $parameters
     * @return \App\Models\Category|\Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function take(array $parameters = []);

    /**
     * @param array $parameters
     * @return \App\Models\Category
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function store(array $parameters);

    /**
     * @param $id
     * @return \App\Models\Category
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function findById($id);

    /**
     * @param $id
     * @param array $parameters
     * @return \App\Models\Category
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function modify($id, array $parameters);

    /**
     * @param $id
     * @return boolean|null
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function destroy($id);
}
