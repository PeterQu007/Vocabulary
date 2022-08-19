<?php

require_once __DIR__ . "/inc/bootstrap.php";

$dbEnglishWords = new EnglishVocModel();

$words = $dbEnglishWords->getEnglishWords(5);

$dbChineseWords = new ChineseVocModel();

$chinese_words = $dbChineseWords->getChineseWords("协调", 100);

$dbChineseChars = new ChineseCharModel();

$chinese_chars = $dbChineseChars->getChineseChar(100);


$dbChineseStemWords = new ChineseStemWordModel();

$chinese_stem_words = $dbChineseStemWords->getChineseStemWord("协调", 100);

var_dump($words);

var_dump($chinese_chars);

var_dump($chinese_stem_words);

var_dump($chinese_words);
