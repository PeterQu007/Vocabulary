<?php
        include_once(dirname(__FILE__) . "/db/readAndSplitChinese.php");
        include_once(dirname(__FILE__) . "/db/saveChineseWords.php");

        $transmuted_words = transmute_english_words(1000, $pdo);
        $chinese_words = $transmuted_words["chinese_words"];
        $english_words = $transmuted_words["english_words"];

        foreach ($chinese_words as $cw) {
          print $cw["chinese_word"] . " : ";
          print $cw["english_word"] . "<br/>";
        }

        $result = save_chinese_words($chinese_words, $pdo);
        print $result;

        if ($result) {
          // UPDATE the state of the english vocabular
          update_english_words($english_words, $pdo);
        }
