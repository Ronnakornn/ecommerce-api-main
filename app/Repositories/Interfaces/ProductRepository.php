<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface ProductRepository
{
    /**
     * @param array $parameters
     * @return \App\Models\Product|\Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function take(array $parameters = []);

    /**
     * @param array $parameters
     * @return \App\Models\Product
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function store(array $parameters);

    /**

     * @return \App\Models\Product
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function storeExcelFile($parameters);

    /**
     * @param $id
     * @return \App\Models\Product
     * @throws \App\Repositories\Exceptions\RepositoryException
     */
    public function findById($id);

    /**
     * @param $id
     * @param array $parameters
     * @return \App\Models\Product
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
