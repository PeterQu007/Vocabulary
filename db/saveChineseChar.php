<?php
include_once('pdoConn.php');

$sql_query_chinese_voc = "SELECT DISTINCT chinese_word FROM chinese_voc";

$stmt = $pdo->query($sql_query_chinese_voc);
$words = array();

while ($word = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $words[] = $word;
}

foreach ($words as $word) {
  // echo $word["chinese_word"] . "<br/>";
  $REGEX_REMOVE_CHINSE_ADJ_ADV_SUFFIX = "/(地|的)$/mu";
  $chinese_stem_word = preg_replace($REGEX_REMOVE_CHINSE_ADJ_ADV_SUFFIX, "", $word['chinese_word'], 1);
  $chinese_stem_words[] = $chinese_stem_word;
}

$chinese_stem_words = array_unique($chinese_stem_words);
asort($chinese_stem_words);

$sql_insert_chinese_word = "INSERT INTO chinese_word (chinese_word) VALUE(?) 
                            ON DUPLICATE KEY UPDATE chinese_word = chinese_word";

$stmt = $pdo->prepare($sql_insert_chinese_word);

try {
  $pdo->beginTransaction();

  foreach ($chinese_stem_words as $cw) {
    echo $cw . "<br/>";
    $stmt->execute(array($cw));
  }

  $pdo->commit();
} catch (Exception $e) {
  $pdo->rollBack();
  echo $e . "<br/>";
}

echo "all done <br/>";
