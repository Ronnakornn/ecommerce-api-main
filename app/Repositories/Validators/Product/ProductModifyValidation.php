<?php

namespace App\Repositories\Validators\Product;

use App\Repositories\Validators\BaseValidator;
use Illuminate\Validation\Rule;

class ProductModifyValidation extends BaseValidator
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'max:100'],
            'description' => ['nullable'],
            'brand_id'=> ['sometimes', 'required'],
            'category_id'=> ['sometimes', 'required'],
            'product_attr' => ['sometimes','required', 'array'],
            'product_attr.*'=> ['sometimes','required'],
            'amount'=> ['sometimes', 'required', 'integer'],
            'warranty'=> ['sometimes', 'max:100'],
            'remark'=> ['sometimes'],
            'type'=> ['sometimes', 'required', 'string', Rule::in(['instock', 'preorder'])],
            'status'=> ['sometimes', 'required', 'string', Rule::in(['enable', 'disable'])],
        ];
    }
}
