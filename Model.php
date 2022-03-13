<?php

namespace app\core;

abstract class Model
{
  public const RULE_REQUIRED = 'required';
  public const RULE_EMAIL = 'email';
  public const RULE_MIN = 'min';
  public const RULE_MAX = 'max';
  public const RULE_MATCH = 'match';
  public const RULE_UNIQUE = 'unique';
  public const RULE_DEFAULT = 'default';

  abstract public function rules(): array;

  abstract public function labels(): array;

  public function label(string $attribute)
  {
    return $this->labels()[$attribute]
      ?? implode(array: preg_split('/(?=[A-Z])/', ucfirst($attribute)), separator: ' ');
  }

  public array $errors = [];

  public function load($data): void
  {
    foreach ($data as $key => $value) {
      if (property_exists($this, $key)) {
        $this->{$key} = $value;
      }
    }
  }

  public function validate(): bool
  {
    foreach ($this->rules() as $attribute => $rules) {
      $value = $this->{$attribute};
      foreach ($rules as $rule) {
        $ruleName = is_string($rule) ? $rule : $rule[0];
        match ($ruleName) {
          self::RULE_REQUIRED => $this->validateRequired($attribute, $value),
          self::RULE_EMAIL => $this->validEmail($attribute, $value),
          self::RULE_MIN => $this->validMin($attribute, $value, $rule),
          self::RULE_MAX => $this->validMax($attribute, $value, $rule),
          self::RULE_MATCH => $this->validMatch($attribute, $value, $rule),
          self::RULE_UNIQUE => $this->validUnique($attribute, $value, $rule),
          default => $this->addError($attribute, self::RULE_DEFAULT)
        };
      }
    }

    return empty($this->errors);
  }

  private function validateRequired($attribute, $value): void
  {
    if (!$value) {
      $this->addError($attribute, self::RULE_REQUIRED);
    }
  }

  private function validEmail($attribute, $value): void
  {
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
      $this->addError($attribute, self::RULE_EMAIL);
    }
  }

  private function validMin($attribute, $value, $rule): void
  {
    if (strlen($value) < $rule['min'] && !$this->errors[$attribute][self::RULE_REQUIRED]) {
      $this->addError($attribute, self::RULE_MIN, $rule);
    }
  }

  private function validMax($attribute, $value, $rule): void
  {
    if (strlen($value) > $rule['max']) {
      $this->addError($attribute, self::RULE_MAX, $rule);
    }
  }

  private function validMatch($attribute, $value, $rule): void
  {
    if ($value !== $this->{$rule['match']}) {
      $rule['match'] = $this->label($rule['match']);
      $this->addError($attribute, self::RULE_MATCH, $rule);
    }
  }

  private function validUnique($attribute, $value, $rule): void
  {
    $class = $rule['class'];
    $uniqueAttribute = $rule['attribute'] ?? $attribute;
    $table = $class::table();
    $statement = $this->prepare("SELECT * FROM {$table} WHERE {$uniqueAttribute} = :attribute");
    $statement->bindValue(":attribute", $value);
    $statement->execute();
    $record = $statement->fetchObject();
    if ($record) {
      $this->addError($attribute, self::RULE_UNIQUE, $rule);
    }
  }

  private function addError(string $attribute, string $rule, $params = []): void
  {
    $params = ['field' => $this->labels()[$attribute] ?? ucfirst($attribute), ...$params];

    $message = match ($rule) {
      self::RULE_REQUIRED => '{field} is required',
      self::RULE_EMAIL => '{field} must be valid email address',
      self::RULE_MIN => '{field} min length must be {min}',
      self::RULE_MAX => '{field} max length must be {max}',
      self::RULE_MATCH => '{field} must be the same as {match}',
      self::RULE_UNIQUE => '{field} must unique',
      self::RULE_DEFAULT =>  '{field} is invalid'
    };

    foreach ($params as $key => $value) {
      $message = str_replace("{{$key}}", $value, $message);
    }

    $this->errors[$attribute][$rule] = $message;
  }

  public function addErrorMessage(string $attribute, string $message)
  {
    $this->errors[$attribute][] = $message;
  }

  public function hasErrors(string $attribute): bool
  {
    return !empty($this->errors[$attribute]);
  }

  public function firstError(string $attribute): mixed
  {
    return !empty($this->errors[$attribute]) ? array_values($this->errors[$attribute])[0] : false;
  }

  public static function prepare(string $sqlQuery)
  {
    return Application::$app->database->prepare($sqlQuery);
  }
}
