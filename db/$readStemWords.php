<?php
// 练习代码, 熟悉去英文词汇的前缀/后缀
// 文件名以$符号开头
// 参考: https://dev.mysql.com/worklog/task/?id=2423
// 摘要:
/*
If someone has a preference for another algorithm, fine,    
it's something to discuss. If not, we'll go with the    
"Porter stemming algorithm" (also known as the "Porter    
stemmer") because it's popular.    
    
The Porter stemmer removes affixes from English words.    
The number of vowels followed by consonants must be > 1    
(words which are very short probably have no affixes).    
Step 1: remove affixes for plurals and participles.    
Examples: "rained" to "rain", "rains" to "rain".    
Step 2: remove common suffixes.    
Example: "raininess" to "raini"    
Step 3: remove special word endings    
Example: "bountiful" to "bounti"    
Step 4: Repeat steps 1-3 until no more affixes appear.    
Step 5: If result ends in a vowel or doublet, possibly remove.    
Example: after stripping "kidnapped" to "kidnapp", remove a "p".    
Example: after stripping "raininess" to "raini", change to "rain".    
For the full C code, see the References section of this task.    
    
Algorithms like this are not particularly good if    
the variation is not done with affixes, e.g. "sang"/"sung".    
Since Oracle can handle "sang"/"sung" and "mouse"/"mice"    
and so on, it will look better.    
*/
include_once('..\vendor\PorterStemmer.php');
$search = "using a dictionary feels as if I’m prying open an oyster rather than falling down a rabbit hole. committed unreliable kidnapped rains cautionary preliminary";
$search = trim(preg_replace('/[^A-Za-z0-9_\s]/', '', $search)); //remove undesired characters
$words = explode(" ", trim($search));
$stemmedSearch = "";
$unstemmedSearch = "";
foreach ($words as $word) {
  $stemmedSearch .= PorterStemmer::Stem($word) . " "; //we add the wildcard after each word
  $unstemmedSearch = $word . " "; //to search the artist column which is not stemmed
}
$stemmedSearch = trim($stemmedSearch);
$unstemmedSearch = trim($unstemmedSearch);

if ($stemmedSearch == "*" || $unstemmedSearch == "*") {
  //otherwise mySql will complain, as you cannot use the wildcard alone
  $stemmedSearch = "";
  $unstemmedSearch = "";
} else {
  echo $stemmedSearch;
  echo '<br/>';
  echo $unstemmedSearch;
}
