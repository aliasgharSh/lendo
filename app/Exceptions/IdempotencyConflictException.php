<?php

namespace App\Exceptions;

use DomainException;

class IdempotencyConflictException extends DomainException
{
    public function __construct(
        string $message = 'This Idempotency-Key was already used with different parameters.'
    ) {
        parent::__construct($message);
    }
}
