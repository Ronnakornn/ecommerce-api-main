<?php

namespace App\Repositories\Validators\Product;

use App\Repositories\Validators\BaseValidator;
use Illuminate\Validation\Rule;

class ProductStoreFileValidation extends BaseValidator
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'uploaded_file' => ['required', 'mimes:xlsx, csv, xls']
        ];
    }

}