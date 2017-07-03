<?php

if (!defined('translate')) {
    die('Direct access not permitted');
}

for ($i = 0; $i < count($paths); $i++) {
    $path = $paths[$i];

    $handle = fopen($public . $path . "/$targetFile", "r");
    $json = "{";
    $first_line = fgets($handle);
    while ($line = fgets($handle)) {
        if (!$line || strpos($line, "$.extend(") > -1 || strpos($line, "});") > -1 || strpos($line, "//") > -1) {
            continue;
        }
        $json .= $line;
    }
    $last_line = $line;
    $json = rtrim($json, ','); //remove last wrong commas
    $json .= "}";
    //echo $json;

    $arr = json_decode($json);
    if (!is_object($arr)) {
        die("!is_object: $json");
    }

    for ($j = 0; $j < count($languages); $j++) {
        $language = $languages[$j];
        //if file to translate is target, ignore
        if (explode(".", $targetFile)[0] == $language) {
            continue;
        }

        $plain = "";
        foreach ($arr as $key => $value) {
            $plain .= str_replace("\n", "<br>", $value) . "\n"; // '\n' NOT ALLOWED ANYMORE!!
        }

        $translated = $trans->translate("en", $language, $plain);

        $translated_arr = explode("\n", $translated);
        echo 'count($translated_arr): ' . count($translated_arr) . "\n";

        $js = "$first_line\n";
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
