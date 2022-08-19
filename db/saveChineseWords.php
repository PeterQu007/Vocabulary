<?php

include_once('pdoConn.php');

// 测试数据
$chinese_words = array(
  array("减少", "n.", "abatement1", "n.减少,降低", "n.减少,降低1"),
  array("缩写", "vt.", "abbreviate1", "vt.缩写;省略", "vt.缩写;省略2")
);

function save_chinese_words($chinese_words, $pdo)
{
  $sql_save_chinese_words = "INSERT INTO chinese_voc 
  (chinese_word, part_of_speech, english_word, word_source, tag0) 
  VALUES (?, ? , ?, ?, ?) ON DUPLICATE KEY 
  UPDATE word_source = ?, tag0 = ?";

  $stmt_insert_chinese_word = $pdo->prepare($sql_save_chinese_words);

  try {
    $pdo->beginTransaction();
    foreach ($chinese_words as $chinese_word) {
      // 提取tag0, 就是[]或者()的内容
      $REGEX_TAG0 = "//mu";
      $word_insert_values = array_merge(array_values($chinese_word), array($chinese_word["word_source"]), array($chinese_word["tag0"]));
      $stmt_insert_chinese_word->execute(
        $word_insert_values
      );
    }
    $pdo->commit();
    return true;
  } catch (Exception $e) {
    $pdo->rollback();
    echo $e;
    return false;
  }
}

function update_english_words($english_words, $pdo)
{
  $sql_update_english_word_state = "UPDATE vocabulary 
  SET transmuted = 1 WHERE voc_id = ?";

  $stmt_update = $pdo->prepare($sql_update_english_word_state);

  try {
    $pdo->beginTransaction();
    foreach ($english_words as $english_word) {
      $stmt_update->execute(array($english_word->voc_id));
    }
    $pdo->commit();
    return true;
  } catch (Exception $e) {
    $pdo->rollback();
    echo $e;
    return false;
  }
}
