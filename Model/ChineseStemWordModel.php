<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class ChineseStemWordModel extends Database
{
  public function getChineseStemWord($char, $limit)
  {
    $sql = "SELECT * FROM chinese_word WHERE chinese_word LIKE '%$char%' LIMIT ?";
    $words = $this->select($sql, array($limit));
    return $words;
  }
}
