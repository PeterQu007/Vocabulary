<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class ChineseCharModel extends Database
{
  public function getChineseChar($limit)
  {
    $sql = "SELECT * FROM chinese_char LIMIT ?";
    $chars = $this->select($sql, array($limit));
    return $chars;
  }
}
