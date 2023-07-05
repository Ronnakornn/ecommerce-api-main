<?php

namespace App\Repositories\Validators\Category;

use App\Repositories\Validators\BaseValidator;
use Illuminate\Validation\Rule;

class CategoryStoreValidation extends BaseValidator
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'sometimes',
                'max:100'
            ],
            'description' => [
                'required',
                'sometimes'
            ],
            'status' => [
                'required',
                'sometimes',
                Rule::in([
                    'enable',
                    'disable'
                ]),
            ]
        ];
    }
}
