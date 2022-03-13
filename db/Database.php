<?php

namespace VanHieu\PhpMvcCore\db;

use VanHieu\PhpMvcCore\Application;
use PDO;

class Database
{
  public PDO $pdo;

  public function __construct(array $config)
  {
    [$dsn, $user, $password] = array_values($config);
    $this->pdo = new PDO($dsn, $user, $password);
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }

  public function applyMigrations()
  {
    $this->createMigrationsTable();
    $applyMigrations = $this->getAppliedMigrations();

    $files = scandir(Application::$ROOT_DIR . "/migrations");

    $toApplyMigrations = array_diff($files, $applyMigrations);
    $newMigrations = [];

    foreach ($toApplyMigrations as $migration) {
      if ($migration === '.' || $migration === '..') {
        continue;
      }

      require_once Application::$ROOT_DIR . '/migrations/' . $migration;

      $className = pathinfo($migration, PATHINFO_FILENAME);
      $instance = new $className();
      $this->log("Migrating {$migration}");
      $instance->up();
      $this->log("Applied migration {$migration}");
      $newMigrations[] = $migration;
    }

    if (!empty($newMigrations)) {
      $this->saveMigrations($newMigrations);
      $this->log("All migrations are applied");
    } else {
      $this->log("There are no migration to apply");
    }
  }

  public function createMigrationsTable()
  {
    $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
      migration VARCHAR(255),
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;");
  }

  public function getAppliedMigrations()
  {
    $statement = $this->pdo->prepare("SELECT migration FROM migrations;");
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_COLUMN);
  }

  public function saveMigrations(array $migrations)
  {
    $migrations = array_map(array: $migrations, callback: fn ($migration) => "('{$migration}')");
    $migrationsValues = implode(array: $migrations, separator: ',');

    $statement = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES {$migrationsValues}");
    $statement->execute();
  }

  public function prepare(string $sqlQuery)
  {
    return $this->pdo->prepare($sqlQuery);
  }

  protected function log(string $message)
  {
    echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
  }
}
