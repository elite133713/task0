<?php

namespace App\Components\Product\Validation\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class ImportableProductConstraint
 *
 * @package App\Components\Product\Validation\Constraints
 */
class ImportableProductConstraint extends Constraint
{
    public $message = 'The string "{{ string }}" contains an illegal character: it can only contain letters or numbers.';

    public $mode = 'strict';
}
