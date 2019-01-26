<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;


class ComplexPasswordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ComplexPassword) {
            throw new UnexpectedTypeException($constraint, ComplexPassword::class);
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

        if (!preg_match('/[0-9]/', $value, $matches) || # number
            !preg_match('/[a-z]/', $value, $matches) || # lowercase character
            !preg_match('/[A-Z]/', $value, $matches)    # uppercase character
        ) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }

    }
}