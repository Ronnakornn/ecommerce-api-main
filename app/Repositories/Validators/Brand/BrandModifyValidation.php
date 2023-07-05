<?php

namespace App\Repositories\Validators\Brand;

use App\Repositories\Validators\BaseValidator;
use Illuminate\Validation\Rule;


class BrandModifyValidation extends BaseValidator
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
        ];
    }
}
