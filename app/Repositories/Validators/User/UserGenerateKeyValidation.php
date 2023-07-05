<?php

namespace App\Repositories\Validators\User;

use App\Repositories\Validators\BaseValidator;

class UserGenerateKeyValidation extends BaseValidator
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'company_id' => [
                'required'
            ],
            'name' => [
                'required'
            ],
        ];
    }

    /**
     * @return array
     */
    protected function messages(): array
    {
        return [
            'company_id.required' => 'The company field is required.'
        ];
    }
}
