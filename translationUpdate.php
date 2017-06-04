<?php

if(!defined('translate')) {
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

for ($i = 0; $i < count($paths); $i++) {
    $path = $paths[$i];

    $handle = fopen($public . $path . "/$targetFile", "r");
    $json = "{";
    while ($line = fgets($handle)) {
        if (!$line || strpos($line, "$.extend(") > -1 || strpos($line, "});") > -1 || strpos($line, "//") > -1) {
            continue;
        }
        $json .= $line;
    }
    $json = rtrim($json, ','); //remove last wrong commas
    $json .= "}";

    $arr = json_decode($json);
    if (!is_object($arr)) {
        die("!is_object: $json");
    }

    for ($j = 0; $j < count($languages); $j++) {
        $language = $languages[$j];
        //if file to translate is target, ignore
        if (explode(".js", $targetFile)[0] == $language) {
            continue;
        }

        $plain = "";
        foreach ($arr as $key => $value) {
            $plain .= str_replace("\n", "<br>", $value) . "\n"; // '\n' NOT ALLOWED ANYMORE!!
        }

        $translated = $trans->translate("en", $language, $plain);

        $translated_arr = explode("\n", $translated);
        echo 'count($translated_arr): ' . count($translated_arr) . "\n";

        $js = "$.extend(window.lang, {\n";
        $k = 0;
        foreach ($arr as $key => $value) {
            $js .= "\"$key\": \"" . str_replace('"', "'", $translated_arr[$k]) . "\",\n";
            $k++;
        }
        $js .= "});";

        file_put_contents($public . $path . "/" . strtolower($language) . ".js", $js);
    }
}

echo "<br><br> done";
