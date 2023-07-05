<?php

namespace App\Repositories\Validators;

use Illuminate\Support\Facades\Validator;

abstract class BaseValidator
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Constructor.
     * @param  array  $attributes
     */
    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param  array  $attributes
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public static function make(array $attributes)
    {
        return (new static($attributes))->createDefaultValidator();
    }

    /**
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    public function createDefaultValidator()
    {
        return Validator::make(
            $this->attributes,
            $this->rules(),
            $this->messages(),
            $this->customAttributes()
        );
    }

    /**
     * @return array
     */
    abstract protected function rules(): array;

    /**
     * @return array
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function customAttributes(): array
    {
        return [];
    }
}
