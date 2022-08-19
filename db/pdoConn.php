<?php

require_once(dirname(__FILE__) . '/db-config.php');

$host = PID_DB_HOST;
$db   = PID_DB_NAME;
$user = PID_DB_USER;
$pass = PID_DB_PASSWORD;
$charset = 'utf8mb4';

$options = [
  \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
  \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
  \PDO::ATTR_EMULATE_PREPARES   => false,
];
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
  $pdo = new \PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
  throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
