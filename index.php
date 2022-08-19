<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vocabulary</title>
</head>

<body>
  <?php
  include_once(__DIR__ . "/inc/bootstrap.php");
  $dbEnglishWord = new EnglishVocModel();
  $dbChineseVoc = new ChineseVocModel();
  $dbChineseChar = new ChineseCharModel();
  $dbChineseStemWord = new ChineseStemWordModel();

  [$chinese_char, $chinese_word, $english_word] = ["", "", ""];
  $chinese_words = [];
  $english_words = [];
  $chinese_chars = [];

  if (isset($_POST['submit1'])) {
    $chinese_char = $_POST["chinese_char"];
    $chinese_word = $_POST["chinese_word"];
    $english_word = $_POST["english_word"];
    $chinese_chars = $chinese_char ? array('chinese_char' => $chinese_char) : $dbChineseChar->getChineseChar(1);

    foreach ($chinese_chars as $cc => $char) {
      $chinese_words = array_merge($chinese_words, $dbChineseVoc->getChineseWords($char, 50));
    }
  }
  if (isset($_POST['submit2'])) {
    $chinese_char = $_POST["chinese_char"];
    $chinese_word = $_POST["chinese_word"];
    $english_word = $_POST["english_word"];
    $chinese_words =  $dbChineseVoc->getChineseWords($chinese_word, 50);
  }
  if (isset($_POST['submit3'])) {
    $chinese_char = $_POST["chinese_char"];
    $chinese_word = $_POST["chinese_word"];
    $english_word = $_POST["english_word"];
    $english_words = $dbEnglishWord->getEnglishWords($english_word, 10);
  }


  ?>

  <form action="" method="POST">
    <p>中文字<input type="text" name="chinese_char" value="<?php echo $chinese_char ?>"><input type="submit" name="submit1" value="Submit"></p>
    <p>中文词汇<input type="text" name="chinese_word" value="<?php echo $chinese_word ?>"><input type="submit" name="submit2" value="Submit"></p>
    <p>English Word<input type="text" name="english_word" value="<?php echo $english_word ?>"><input type="submit" name="submit3" value="Submit"></p>
  </form>
  <hr>
  <h2>中文字</h2>
  <p>
    <span>Previous</span>
    <?php
    try {
      foreach ($chinese_chars as $cc => $char) {
        echo "<span>$char</span>";
      }
    } catch (Exception $e) {
      echo $e;
    }
    ?>
    <span>Next</span>
  </p>
  <h2>中文词汇</h2>
  <p>
    <?php
    foreach ($chinese_words as $cw) {
      $cword = $cw['chinese_word'];
      $eword = $cw['english_word'];
      echo "<span>$cword</span> : <span>$eword</span><br/>";
    }

    ?>
  </p>

  <h2>English Words</h2>
  <p>
    <?php
    foreach ($english_words as $ew) {
      $eword = $ew['word'];
      $cmeaning = $ew['vc_chinese'];
      echo "<span>$eword</span> : <span>$cmeaning</span><br/>";
    }

    ?>
  </p>


</body>

</html>