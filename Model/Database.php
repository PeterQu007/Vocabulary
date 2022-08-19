<?php
class Database
{
  protected $pdo = null;

  public function __construct()
  {
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
      $this->pdo = new \PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
      throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
  }

  public function select($query = "", $params = [])
  {
    try {
      $stmt = $this->pdo->prepare($query);
      $stmt->execute($params);
      while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = $result;
      }
      return $results;
    } catch (PDOException $e) {
      throw new PDOException($e->getMessage(), (int)$e->getCode());
    }
  }
}
