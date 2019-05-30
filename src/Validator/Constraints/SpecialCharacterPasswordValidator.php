<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class SpecialCharacterPasswordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof SpecialCharacterPassword) {
            throw new UnexpectedTypeException($constraint, SpecialCharacterPassword::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');
        }

        if (!preg_match('/@!"#%&\',-:;<=>_`{}~\\\$\(\)\*\+\.\/\?\[\]\^\|/', $value, $matches)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
