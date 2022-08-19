<?php
/*
* PHP代码, 查询生词, 返回中文词义
* 响应Chrome的插件checkWord的请求
* 22-08-17 增加去前缀/后缀的功能
*/
include_once('pdoConn.php');
include_once('..\vendor\PorterStemmer.php');

$text = $_POST;
$debug = false;
$json = file_get_contents("php://input"); // json string
if (!$json) {
  $json = json_encode("Consecutive!    Buoyed planned, separated 'prying' using");
  $debug = true;
}

//read english words
$words = array();

$stmt = null;

$text  = json_decode($json);
$removeUnicode = preg_replace("/\\u[a-f0-9]{4}/m", " ", $text);
// 去掉非词汇符号
// $pureWords = preg_replace("/[^a-zA-Z ]/m", " ", $removeUnicode);
$pureWords = trim(preg_replace('/[^A-Za-z0-9_\-\s]/', '', $removeUnicode));
$removeExtraSpaces = preg_replace("/\s+/m", " ", $pureWords);
// 分割成单词数组
$words = preg_split("/\s+/m", $removeExtraSpaces);

$articleWordsCount = count($words);
$LenghyArticle = $articleWordsCount > 2000; // 2000字以上的文章, 定义为长文, 不做词频统计
// 去掉重复单词
$words = array_unique($words);
$articleWordsCountUnique = count($words);

// remove simple words
$words = array_filter($words, "removeSimpleWords");
function removeSimpleWords($word)
{
  return strlen($word) > 3;
}

// 保存词汇的副本, stem => original_word
// 例如: ['pry' => 'prying']
$words_copy = [];
$words_ready_for_lookup = [];

// 去掉前缀/后缀
foreach ($words as $word) {
  // 转换成小写字符, 检查词干
  $word_lowercase = strtolower($word);
  $word_stem  = PorterStemmer::Stem($word_lowercase);
  $words_ready_for_lookup[] = $word_stem;
  // 如果词汇有形式的变化, 保留副本
  if ($word_stem !== $word_lowercase) {
    $words_copy[$word_stem] = $word;
  }
}

// method 1, use multiple queries
$sql_check_GRE = "SELECT * FROM vocabulary WHERE word = ?";
// $stmt = $pdo->prepare($sql_check_GRE);
$return_words = [];

// foreach ($words as $word) {
//   $word = trim($word);
//   $len = strlen($word);
//   if ($len > 3) {
//     $stmt->execute(array($word));
//     while ($return = $stmt->fetch(PDO::FETCH_ASSOC)) {
//       $return_words[] = $return;
//     }
//   }
// }

// method 2, use IN operater
// $sql_check_GRE_2 = "SELECT * FROM vocabulary WHERE word IN (?)";
// foreach ($words as $word) {
//   $word_creteria .= "'$word',";
// }
$new_words = [];
foreach ($words_ready_for_lookup as $word) {
  $new_words[] = strtolower($word);
}
$totalWords = count($new_words);
$in_params = trim(str_repeat('?, ', $totalWords), ', ');
$sql_check_GRE_2 = "SELECT DISTINCT word, vc_chinese, difficulty, vc_phonetic_us, word_frequency+1 as word_frequency FROM vocabulary WHERE word IN ({$in_params}) AND difficulty >= 7 ORDER BY word;";
try {
  $stmt = $pdo->prepare($sql_check_GRE_2);
  $stmt->execute($new_words);
} catch (PDOException $e) {
  echo $e;
}

$words_for_word_frequency = [];
while ($return = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $return_words[] = $return;
  $words_for_word_frequency[] = $return["word"];
}

// 备注: 如果文章超过2000字, 就不要统计词频了.
// 长文章很少会读下去的.
if (!($debug || $LenghyArticle)) {
  // 如果不是调试程序, 更新词频
  // 更新生词表的词频数量, 记录生词出现的次数
  // 
  $total_return_words = count($return_words);
  $in_params_for_word_frequency_update = trim(str_repeat('?, ', $total_return_words), ', ');
  $sql_update_word_frequency = "UPDATE vocabulary SET word_frequency = word_frequency +1 WHERE word IN ({$in_params_for_word_frequency_update})";
  try {
    $stmt = $pdo->prepare($sql_update_word_frequency);
    $stmt->execute($words_for_word_frequency);
  } catch (PDOException $e) {
    echo $e;
  }
}

// 恢复前缀/后缀, 准备返回查询结果
$word_keys = array_keys($words_copy);
foreach ($return_words as $word_index => $return_word) {
  $search_stem = $return_word['word'];
  $return_words[$word_index]['word_stem'] = $search_stem; //保存词干
  $index = array_search($search_stem, $word_keys);
  if ($index > -1) {
    $return_words[$word_index]['word'] = $words_copy[$search_stem]; //取得原型
  }
}

$returnInfo = array(
  'totalWords' => $articleWordsCount,
  'totalWordsUnique' => $articleWordsCountUnique,
  'newWordsCount' => count($return_words),
  'newWords' => $return_words
);

echo json_encode($returnInfo);
