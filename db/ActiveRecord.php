<?php

namespace VanHieu\PhpMvcCore\db;

use VanHieu\PhpMvcCore\Model;

abstract class ActiveRecord extends Model
{
  abstract static public function table(): string;

  abstract public function attributes(): array;

  abstract public static function primaryKey(): string;

  public function save()
  {
    $table = $this->table();
    $attributes = $this->attributes();
    $params = array_map(array: $attributes, callback: fn ($attribute) => ":{$attribute}");
    $statement = self::prepare("INSERT INTO {$table} (" . implode(',', $attributes) . ") 
      VALUES(" . implode(',', $params) . ")");

    foreach ($attributes as $attribute) {
      $statement->bindValue(":{$attribute}", $this->{$attribute});
    }

    $statement->execute();
    return true;
  }

  public static function findOne(array $where)
  {
    $table = static::table();

    $attributes = array_keys($where);

    $whereQuery = implode(
      array: array_map(array: $attributes, callback: fn ($attribute) => "{$attribute} = :{$attribute}"),
      separator: " AND "
    );

    $sqlQuery = "SELECT * FROM {$table} WHERE {$whereQuery}";
    $statement = self::prepare($sqlQuery);

    foreach ($where as $key => $value) {
      $statement->bindValue(":{$key}", $value);
    }

    $statement->execute();
    return $statement->fetchObject(static::class);
  }
}
