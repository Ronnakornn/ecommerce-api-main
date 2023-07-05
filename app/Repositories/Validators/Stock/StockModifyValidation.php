<?php

namespace App\Repositories\Validators\Stock;

use App\Repositories\Validators\BaseValidator;

class StockModifyValidation extends BaseValidator
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'product_ids' => [
                'sometimes',
            ],
            'lot' => [
                'sometimes',
                'required',
                'date_format:Y-m-d H:i'
            ],
            'amount' => [
                'sometimes',
                'required',
                'integer',
            ],
            'description' => [
                'sometimes',
                'max:255',
            ],
            'cost' => [
                'sometimes',
                'required',
                'regex:/^\d{1,8}(\.\d{1,2})?$/',
            ],
        ];
    }
}
