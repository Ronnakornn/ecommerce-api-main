<?php

namespace App\Repositories\Exceptions;

use Illuminate\Support\Arr;
use Throwable;
use Exception;

class RepositoryException extends Exception
{
    private $errors;
    private $errorCode;
    private $errorMessage;
    /**
     * RepositoryException constructor.
     * @param array $errors
     * @param int $statusCode
     * @param int $errorCode
     * @param Throwable|null $previous
     */
    public function __construct(
        int $errorCode = 0,
        array $errors = [],
        int $statusCode = 422,
        Throwable $previous = null
    ) {
        $this->errorCode = $errorCode;
        $this->errors = $errors;
        $errorCodes = config('error_code');

        foreach ($errorCodes as $package => $codes) {
            if (key_exists($errorCode, $codes)) {
                $this->errorMessage = $codes[$errorCode];
                break;
            }
        }

        if (empty($this->errorMessage)) {
            $this->errorMessage = "Error code:{$this->errorCode} not found, please check package config.";
        }

        parent::__construct($this->errorMessage, $statusCode, $previous);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return integer
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}
