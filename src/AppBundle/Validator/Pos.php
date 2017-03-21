<?php

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Pos extends Constraint {
    public $message = 'Topic / adjective can only contain alphanumeric characters and cannot consist of multiple words.';
}