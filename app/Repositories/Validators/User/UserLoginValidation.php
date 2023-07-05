<?php

namespace App\Repositories\Validators\User;

use App\Repositories\Validators\BaseValidator;

class UserLoginValidation extends BaseValidator
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'email' => [
                'required'
            ],
            'password' => [
                'required'
            ],
        ];
    }

    /**
     * @return array
     */
    protected function messages(): array
    {
        return [];
    }
}
