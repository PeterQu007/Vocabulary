<?php
include_once('pdoConn.php');
// Constant
$PART_OF_SPEECH = array('n.', 'vt.', 'adj.', 'adv.', 'v.');
$SEARCH_POS_IN_WORD = "/(n|vt|adj|adv|pro)/";

// Read a english word from vocabulary table
// read min Voc ID
$minVocID;
$sql_read_word_min_id = 'SELECT min(voc_id) min_id FROM vocabulary';
$stmt = $pdo->query($sql_read_word_min_id);

while ($word_min_id = $stmt->fetch(PDO::FETCH_OBJ)) {
  $minVocID = $word_min_id->min_id;
}
$stmt = null;

// 英文->中文 转换子程序
function transmute_english_words($count, $pdo)
{
  $sql_read_words = "SELECT * FROM vocabulary WHERE transmuted = 0 limit $count ";
  // //TEST QUERY FOR A SPECIFIC WORD - 有后缀
  // $sql_read_words = "SELECT * FROM vocabulary WHERE word='arresting'";
  // //TEST QUERY FOR A SPECIFIC WORD - 有前缀/tag0
  // $sql_read_words = "SELECT * FROM vocabulary WHERE word='audit'";

  $stmt = $pdo->query($sql_read_words);

  $words = array();
  $chinese_words = array();

  while ($word = $stmt->fetch(PDO::FETCH_OBJ)) {
    $words[] = $word;
  }

  // 把每个词的中文意思进行分割
  foreach ($words as $w) {
    //eg: abrasive - adj. 粗糙的；有研磨作用的；伤人感情的 n. 研磨料
    $chinese_word_groups = split_chinese_by_pos(($w->vc_chinese));

    foreach ($chinese_word_groups as $chinese_word_group) {
      $chinese_single_words = split_chinese_word($chinese_word_group[0]);
      $word_pos = $chinese_word_group[1];
      foreach ($chinese_single_words as $chinese_single_word) {
        // print "$word_pos | $chinese_single_word | $chinese_word_group[0] | $w->word <br/>";
        $tag0 = get_chinese_tag0($chinese_single_word);
        $suffix = get_chinese_suffix($chinese_single_word);
        $chinese_pure_single_word = get_chinese_word($chinese_single_word);
        $chinese_word = array(
          "chinese_word" => $chinese_pure_single_word,
          "part_of_speech" => $word_pos,
          "english_word" => $w->word,
          "word_source" => $chinese_word_group[0],
          "tag0" => $tag0[0]
        );
        // var_dump($chinese_word);
        $chinese_words[] = $chinese_word;
      }
    }
  }

  $result = array(
    "chinese_words" => $chinese_words,
    "english_words" => $words
  );
  return $result;
}

function split_chinese_by_pos($word)
{
  //eg: abrasive - adj. 粗糙的；有研磨作用的；伤人感情的 n. 研磨料
  $SPLIT_POS = "/(n.|vt.|vi.|v.|adj.|adv.)(\s?[\x{4e00}-\x{9fa5}]+，?,?；?;?\s?)*/mu";
  $REGEX_SPLIT_POS = "/(n\.|vt\.|vi\.|v\.|adj\.|prep\.|pron\.|adv\.|conj\.|int\.|num\.)(\s?((\[|（)[\x{4e00}-\x{9fa5}]*(\]|）))?\s?[\x{4e00}-\x{9fa5}]+…?…?[\x{4e00}-\x{9fa5}]+((\[|（)[\x{4e00}-\x{9fa5}a-z\s,]*(\]|）))?，?,?；?;?\s?)*/mu";

  preg_match_all($REGEX_SPLIT_POS, $word, $word_groups_by_pos, PREG_SET_ORDER);
  // return: ["adj. 粗糙的；有研磨作用的；伤人感情的", "n. 研磨料"] 
  // group 1: adj. 粗糙的；有研磨作用的；伤人感情的
  // group 2: n. 研磨料
  return $word_groups_by_pos;
}

function split_chinese_word($word)
{
  //eg: abrasive - adj. 粗糙的；有研磨作用的；伤人感情的
  $SPLIT_CHINESE_WORD = "/([\x{4e00}-\x{9fa5}]+)/mu";
  $REGEX_SPLIT_CHINESE_WORD = "/((\[|（)[\x{4e00}-\x{9fa5}]*(\]|）))?\s?([\x{4e00}-\x{9fa5}]+…?…?[\x{4e00}-\x{9fa5}]+((\[|（)[\x{4e00}-\x{9fa5}a-z\s,]*(\]|）))?)/mu";

  preg_match_all($REGEX_SPLIT_CHINESE_WORD, $word, $single_chinese_words);
  // return ["粗糙的","有研磨作用的","伤人感情的"]
  return $single_chinese_words[0];
}

function get_chinese_tag0($word)
{
  $REGEX_GET_TAG0 = "/((\[|（)[\x{4e00}-\x{9fa5}]*(\]|）))?/mu";
  preg_match_all($REGEX_GET_TAG0, $word, $tag0);
  return $tag0[0];
}

function get_chinese_suffix($word)
{
  $REGEX_GET_SUFFIX = "/（[\x{4e00}-\x{9fa5}a-z\s,]*）$/mu";
  preg_match_all($REGEX_GET_SUFFIX, $word, $suffix);
  return $suffix[0];
}

function get_chinese_word($word)
{
  $REGEX_GET_TAG0 = "/((\[|（)[\x{4e00}-\x{9fa5}]*(\]|）))?/mu";
  $REGEX_GET_SUFFIX = "/（[\x{4e00}-\x{9fa5}a-z\s,]*）$/mu";
  $word = preg_replace($REGEX_GET_TAG0, "", $word);
  $word = preg_replace($REGEX_GET_SUFFIX, "", $word);
  $word = trim($word);
  return $word;
}
