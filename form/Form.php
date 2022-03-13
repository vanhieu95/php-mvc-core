<?php

namespace app\core\form;

use app\core\Model;

class Form
{
  public static function begin($action, $method): Form
  {
    echo sprintf('<form action="%s" method="%s">', $action, $method);
    return new Form();
  }

  public static function end(): void
  {
    echo '</form>';
  }

  public function field(Model $model, string $attribute): InputField
  {
    return new InputField($model, $attribute);
  }

  public function textarea(Model $model, string $attribute): TextAreaField
  {
    return new TextAreaField($model, $attribute);
  }
}
