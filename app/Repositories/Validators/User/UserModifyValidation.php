<?php

namespace App\Repositories\Validators\User;

use App\Models\User;
use App\Repositories\Validators\BaseValidator;
use Illuminate\Validation\Rule;

class UserModifyValidation extends BaseValidator
{
    protected $user;

    public function __construct(array $attributes, User $user)
    {
        parent::__construct($attributes);
        $this->user = $user;
    }

    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'sometimes',
            ],
            'email' => [
                'required',
                'sometimes',
                'email',
                Rule::unique('users')
                    ->whereNull('deleted_at')
                    ->whereNotNull('email'),
            ],
            'password' => [
                'required',
                'sometimes',
                'without_spaces',
                'min:4',
                'regex:/(?=.*\\d)(?=.*[a-z])(?=.*[A-Z])/'
            ],
            'c_password' => [
                'required',
                'sometimes',
                'same:password',
            ],
            'user_role' => [
                'required',
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
                'sometimes',
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
