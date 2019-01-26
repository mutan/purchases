<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ComplexPassword extends Constraint
{
    public $message = 'Password must be complex';
}
