<?php

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

$public = "../would-you-rather/";
$paths = array(
    "~lang",
//    "~modules/tutorial/lang",
//    "~commons/lang",
//    "~commons/modules/like/lang",
//    "~commons/modules/polls/lang",
//    "~commons/modules/rate/lang",
//    "~commons/modules/report/lang",
//    "~commons/modules/tutorial/lang"
);

require 'languages.php';
//$languages = array();
$languages = array("es");

for ($i = 0; $i < count($paths); $i++) {
    $path = $paths[$i];

    $handle = fopen($public . $path . "/en.js", "r");
    $json = "{";
    while ($line = fgets($handle)) {
        if ($line && false === strpos($line, "$.extend(") && false === strpos($line, "});") && false === strpos($line, "//")) {
            $json .= $line;
        }
    }
    $json .= "}";

//    echo $json;
    $arr = json_decode($json);
//    echo json_encode($arr);

    for ($j = 0; $j < count($languages); $j++) {
        $language = $languages[$j];
        if ("en" == $language) {
            continue;
        }

        //$translated = array();
        $plain = "";
        foreach ($arr as $key => $value) {
            //$translated[$key] = $trans->translate("en", $language, $value);
            $plain .= str_replace("\n", "<br>", $value) . "\n"; // '\n' NOT ALLOWED!!
        }

        $translated = $trans->translate("en", $language, $plain);

        $translated_arr = explode("\n", $translated);
        echo 'count($translated_arr): ' . count($translated_arr);

        $js = "$.extend(window.lang, {\n";
        $k = 0;
        foreach ($arr as $key => $value) {
            $js .= "'$key': \"" . str_replace('"', "'", $translated_arr[$k]) . "\",\n";
            $k++;
        }
        $js .= "});";

        //echo $js;

        file_put_contents($public . $path . "/" . strtolower($language) . ".js", $js);
    }
}

echo "<br><br> done";
