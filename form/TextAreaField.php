<?php

namespace app\core\form;

class TextAreaField extends BaseField
{
  public function renderInput(): string
  {
    return sprintf(
      '<textarea name="%s" id="%s" class="form-control %s">%s</textarea>',
      $this->attribute,
      $this->attribute,
      $this->model->hasErrors($this->attribute) ? 'is-invalid' : '',
      $this->model->{$this->attribute}
    );
  }
}
