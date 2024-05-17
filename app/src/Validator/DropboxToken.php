<?php

namespace MateuszJagielskiRekrutacjaSmartiveapp\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class DropboxToken extends Constraint
{
  public $message = 'Token is required when destination is set to "Upload to Dropbox".';

  public function __construct(?string $message = null, ?array $groups = null, $payload = null)
  {
    parent::__construct([], $groups, $payload);
    $this->message = $message ?? $this->message;
  }

}
