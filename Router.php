<?php

namespace VanHieu\PhpMvcCore;

use VanHieu\PhpMvcCore\exception\NotFoundException;

class Router
{
  protected array $routers = [];

  public function __construct(
    public Request $request,
    public Response $response
  ) {
  }

  /**
   * Store to routers the get route
   *
   * @param string $path
   * @param callable|string|array $callback
   * @return void
   */
  public function get(string $path, callable|string|array $callback): void
  {
    $this->routers['get'][$path] = $callback;
  }

  /**
   * Store to routers the post route
   *
   * @param string $path
   * @param callable|array|string $callback
   * @return void
   */
  public function post(string $path, callable|array|string $callback): void
  {
    $this->routers['post'][$path] = $callback;
  }

  public function resolve(): mixed
  {
    $path = $this->request->getPath();
    $method = $this->request->method();

    $callback = $this->routers[$method][$path] ?? false;

    if (!$callback) {
      throw new NotFoundException();
    }

    if (is_string($callback)) {
      return Application::$app->view->renderView($callback);
    }

    if (is_array($callback)) {
      Application::$app->setController(new $callback[0]());
      $controller = $callback[0] = Application::$app->getController();
      Application::$app->getController()->setAction($callback[1]);

      $middlewares = $controller->getMiddlewares();
      foreach ($middlewares as $middleware) {
        $middleware->execute();
      }
    }

    return call_user_func($callback, $this->request, $this->response);
  }
}
