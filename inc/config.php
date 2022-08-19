<?php

if (!defined('PID_DB_NAME')) {
  define('PID_DB_NAME', 'english_words');
}

/** MySQL database username */
if (!defined('PID_DB_USER')) {
  define('PID_DB_USER', 'root');
}

/** MySQL database password */

if (!defined('PID_DB_PASSWORD')) {
  define('PID_DB_PASSWORD', '');
}

/** MySQL hostname */
if (!defined('PID_DB_HOST')) {
  define('PID_DB_HOST', '127.0.0.1'); // change from localhost to 127.0.0.1 for https 
}
