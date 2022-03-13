<?php

namespace app\core\exception;

use Exception;

class ForbiddenException extends Exception
{
  protected $message = 'You don\'t have permission to access';
  protected $code = 403;
}
