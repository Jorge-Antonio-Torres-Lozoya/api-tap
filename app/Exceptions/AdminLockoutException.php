<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Thrown when an operation would leave the system without any administrator
 * (a user whose profiles grant access to every section), which would lock
 * everyone out of user and profile management.
 */
class AdminLockoutException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'errors' => null,
        ], 409);
    }
}
