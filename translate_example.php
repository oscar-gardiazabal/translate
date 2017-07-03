<?php

define('translate', TRUE);

require 'translation/load.php';

$public = "/";
$targetFile = "en.js";

$paths = array(
    "~lang"
);

//load:
define('translate', TRUE);
include 'translationUpdate.php';
