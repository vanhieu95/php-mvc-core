<?php

namespace VanHieu\PhpMvcCore\form;

use VanHieu\PhpMvcCore\Model;

abstract class BaseField
{
  public function __construct(public Model $model, public string $attribute)
  {
  }

  public function __toString()
  {
    return sprintf(
      '<div class="form-group mb-3">
        <label for="%s" class="form-label">%s</label>
        %s
        <div class="invalid-feedback">
          %s
        </div>
      </div>',
      $this->attribute,
      $this->model->label($this->attribute),
      $this->renderInput(),
      $this->model->firstError($this->attribute)
    );
  }

  abstract public function renderInput(): string;
}
