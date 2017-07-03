<?php

for ($i = 0; $i < count($paths); $i++) {
    $path = $paths[$i];
    $json = file_get_contents($public . $path . "/" . $targetFile);
    $arr = json_decode($json);

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

        $json_new = "[\n";
        for ($i = 0; $i < count($translated_arr); $i++) {
            $json_new .= "\"" . str_replace('"', "'", $translated_arr[$i]) . "\",\n";
        }
        $json_new = rtrim(rtrim($json_new), ','); //remove last ','
        $json_new .= "\n]";

        file_put_contents($public . $path . "/" . strtolower($language) . ".json", $json_new);
    }
}

echo "<br><br> json done";
