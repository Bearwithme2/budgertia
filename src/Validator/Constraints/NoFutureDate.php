<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
class NoFutureDate extends Constraint
{
    public string $message = 'Date cannot be in the future.';

    public function validatedBy(): string
    {
        return static::class . 'Validator';
    }
}
