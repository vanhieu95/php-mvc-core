<?php

namespace VanHieu\PhpMvcCore\middlewares;

use VanHieu\PhpMvcCore\Application;
use VanHieu\PhpMvcCore\exception\ForbiddenException;

class AuthMiddleware extends BaseMiddleware
{
  public function __construct(public array $actions = [])
  {
  }

  public function execute()
  {
    if (Application::isGuest()) {
      if (
        empty($this->actions) ||
        in_array(Application::$app->getController()->getAction(), $this->actions)
      ) {
        throw new ForbiddenException();
      }
    }
  }
}
