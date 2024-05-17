<?php

namespace MateuszJagielskiRekrutacjaSmartiveapp\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DropboxTokenValidator extends ConstraintValidator
{
  public function validate(mixed $value, Constraint $constraint): void
  {
    if ($this->context->getRoot()->get('destination')->getData() === 1 && empty($value)) {
      $this->context->buildViolation($constraint->message)
        ->addViolation();
    }
  }
}
