<?php

namespace app\core;

class View
{
  public string $title = '';


  public function renderView(string $view, array $params = []): string
  {
    $viewContent = $this->renderOnlyView($view, $params);
    $layoutContent = $this->layoutContent();
    return str_replace('{{ content }}', $viewContent, $layoutContent);
  }

  public function renderContent(string $viewContent): string
  {
    $layoutContent = $this->layoutContent();
    return str_replace('{{ content }}', $viewContent, $layoutContent);
  }

  public function layoutContent(): string
  {
    $layout = Application::$app->getController()?->layout ?? Application::$app->layout;
    ob_start();
    include_once Application::$ROOT_DIR . "/views/layout/{$layout}.php";
    return ob_get_clean();
  }

  public function renderOnlyView(string $view, array $params): string
  {
    foreach ($params as $key => $value) {
      $$key = $value;
    }

    ob_start();
    include_once Application::$ROOT_DIR . "/views/{$view}.php";
    return ob_get_clean();
  }
}
