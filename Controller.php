<?php

namespace VanHieu\PhpMvcCore;

use VanHieu\PhpMvcCore\middlewares\BaseMiddleware;

class Controller
{
  public string $layout = 'main';
  private string $action = '';

  /**
   * @var BaseMiddleware[] 
   */
  protected array $middlewares = [];

  public function setLayout($layout)
  {
    $this->layout = $layout;
  }

  public function render(string $view, array $params = []): string
  {
    return Application::$app->view->renderView($view, $params);
  }

  public function registerMiddleware(BaseMiddleware $middleware)
  {
    $this->middlewares[] = $middleware;
  }

  public function setAction(string $action)
  {
    $this->action = $action;
  }

  public function getAction(): string
  {
    return $this->action;
  }

  public function setMiddlewares(BaseMiddleware $middleware)
  {
    $this->middlewares[] = $middleware;
  }

  /**
   * @return BaseMiddleware[]
   */
  public function getMiddlewares(): array
  {
    return $this->middlewares;
  }
}
