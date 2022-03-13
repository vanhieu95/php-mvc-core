<?php

namespace app\core;

class Request
{
  public function getPath(): string
  {
    $path = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'] ?? '/';

    $positionQuery = strpos($path, '?');

    return $positionQuery ? substr($path, 0, $positionQuery) : $path;
  }

  public function method(): string
  {
    return strtolower($_SERVER['REQUEST_METHOD']);
  }

  public function isGet(): bool
  {
    return $this->method() === 'get';
  }

  public function isPost(): bool
  {
    return $this->method() === 'post';
  }

  public function body(): array
  {
    $body = [];

    if ($this->method() === 'get') {
      foreach ($_GET as $key => $value) {
        $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
      }
    }

    if ($this->method() === 'post') {
      foreach ($_POST as $key => $value) {
        $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
      }
    }

    return $body;
  }
}
