<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    public function __construct($message, $code = 500)
    {
        parent::__construct($message, $code);
    }
    
    public function render()
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error' => $this->getMessage()
        ], $this->getCode() ?? 500);
    }
}
