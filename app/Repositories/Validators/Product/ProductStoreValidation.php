<?php

namespace App\Repositories\Validators\Product;

use App\Repositories\Validators\BaseValidator;
use Illuminate\Validation\Rule;

class ProductStoreValidation extends BaseValidator
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'max:100'],
            'description' => ['nullable'],
            'brand_id'=> ['required'],
            'category_id'=> ['required'],
            'product_attr' => ['sometimes','required', 'array'],
            'product_attr.*'=> ['sometimes','required'],
            'amount'=> ['required', 'integer'],
            'warranty'=> ['sometimes', 'max:100'],
            'remark'=> ['sometimes'],
            'type'=> ['required', 'string', Rule::in(['instock', 'preorder'])],
            'status'=> ['required', 'string',  Rule::in(['enable', 'disable'])],
        ];
    }

}
