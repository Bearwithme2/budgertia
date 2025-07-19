<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NoFutureDateValidator extends ConstraintValidator
{
    /**
     * @param \DateTimeInterface|null $value
     * @param NoFutureDate $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof \DateTimeInterface) {
            return;
        }

        $now = new \DateTimeImmutable();
        if ($value > $now) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
