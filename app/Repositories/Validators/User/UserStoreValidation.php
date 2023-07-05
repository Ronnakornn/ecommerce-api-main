<?php

namespace App\Repositories\Validators\User;

use App\Repositories\Validators\BaseValidator;
use Illuminate\Validation\Rule;

class UserStoreValidation extends BaseValidator
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'name' => [
                'required',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')
                    ->whereNull('deleted_at')
                    ->whereNotNull('email'),
            ],
            'password' => [
                'required',
                'without_spaces',
                'min:4',
                'regex:/(?=.*\\d)(?=.*[a-z])(?=.*[A-Z])/'
            ],
            'c_password' => [
                'required',
                'same:password',
            ],
            'user_role' => [
                'sometimes',
                Rule::in([
                    'superAdmin',
                    'admin',
                    'company',
                    'customer'
                ]),
            ],
            'phone' => [
                'required',
                'without_spaces'
            ],
            'tax_id' => [
                'nullable',
            ],
            'address' => [
                'nullable',
            ],
            'user_img' => [
                'nullable',
            ],

        ];
    }

    /**
     * @return array
     */
    protected function messages(): array
    {
        return [
            'password.regex' => 'Password must be 0-9, a-z, A-Z',
            'user_role.in' => 'User role must be in superAdmin, admin, company',
        ];
    }
}
