<?php
include_once('pdoConn.php');

function read_chinese_chars($count, $pdo)
{
  $sql_read_chinese_chars = "SELECT chinese_char FROM chinese_char LIMIT $count";
  $stmt = $pdo->query($sql_read_chinese_chars);
  while ($char = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $chars[] = $char['chinese_char'];
  }
  return $chars;
}

function read_chinese_words($chinese_char, $pdo)
{
  $sql_read_chinese_words = "SELECT chinese_word FROM chinese_word WHERE chinese_word LIKE '%$chinese_char%'";
  $stmt = $pdo->query($sql_read_chinese_words);
  while ($word = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $words[] = $word['chinese_word'];
  }
  return $words;
}
