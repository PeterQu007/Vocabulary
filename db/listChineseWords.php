<?php
include_once("pdoConn.php");

$sql_query_chinese_words = "SELECT chinese_word, part_of_speech, english_word, tag0, word_source FROM chinese_voc ORDER BY chinese_word";

$stmt = $pdo->query($sql_query_chinese_words);
$chinese_words = array();

while ($chinese_word = $stmt->fetch(PDO::FETCH_OBJ)) {
  $chinese_words[] = $chinese_word;
}

foreach ($chinese_words as $chinese_word) {
  echo "$chinese_word->chinese_word, $chinese_word->part_of_speech, $chinese_word->english_word, $chinese_word->tag0 <br />";
  // var_dump($chinese_word);
}
