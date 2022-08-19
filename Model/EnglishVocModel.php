<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";

class EnglishVocModel extends Database
{
  public function getEnglishWords($word, $limit)
  {
    $sql = "SELECT * FROM vocabulary WHERE word LIKE '%$word%' LIMIT ?";

    return $this->select($sql, array($limit));
  }
}
