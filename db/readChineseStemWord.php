<?php

include_once("pdoConn.php");

$sql_read_chinese_stem_words = "SELECT chinese_word FROM chinese_word";

$stmt = $pdo->query($sql_read_chinese_stem_words);

$chinese_stem_words = [];
$chinese_chars = [];

while ($word = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $chinese_stem_words[] = $word['chinese_word'];
}

// var_dump($chinese_stem_words);

foreach ($chinese_stem_words as $cw) {
  $splited = split_chinese_word_to_char($cw);
  $chinese_chars = array_merge($chinese_chars, $splited);
}

$chinese_chars = array_unique($chinese_chars);
asort($chinese_chars);

// var_dump($chinese_chars);

$sql_insert_chinese_chars = "INSERT INTO chinese_char (chinese_char) VALUE (?) 
                            ON DUPLICATE KEY UPDATE chinese_char = chinese_char";

$stmt = $pdo->prepare($sql_insert_chinese_chars);
try {
  $pdo->beginTransaction();
  foreach ($chinese_chars as $cc) {
    echo $cc . "<br/>";
    $stmt->execute(array($cc));
  };
  $pdo->commit();
} catch (Exception $e) {
  $pdo->rollBack();
  echo $e;
}


function split_chinese_word_to_char($word)
{
  $REGEX_CHINESE_CHAR = "/[\x{4e00}-\x{9fa5}]{1}/mu";
  $splited_chinese_chars = [];
  preg_match_all($REGEX_CHINESE_CHAR, $word, $splited_chinese_chars);
  return array_unique($splited_chinese_chars[0]);
}
