<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class ChineseVocModel extends Database
{
  public function getChineseWords($words, $limit)
  {
    $sql = "SELECT * FROM chinese_voc WHERE chinese_word LIKE '%$words%' LIMIT ?";
    $resultx = array();
    $resultx = $this->select($sql, array($limit));
    return $resultx ?? [];
  }
}
