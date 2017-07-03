<?php

define('translate', TRUE);

if (!defined('translate')) {
    die('Direct access not permitted');
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//$version = phpversion();
//echo "php $version <br>";
$whitelist = array(
    '127.0.0.1',
    '::1'
);
if (!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
    die("ERROR");
}
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
//

require_once ('GoogleTranslate.php');

use \Statickidz\GoogleTranslate;

$trans = new GoogleTranslate();

require 'languages.php';
//$languages = array();
