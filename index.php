<?php

$f3 = require('lib/base.php');
require_once('plugins/phpmorphy/src/common.php');
// require_once('plugins/pdftotext/PdfToText.php');

// System configuration
$f3->config(".env");
$f3->set("AUTOLOAD", "src/; plugins/");
$f3->set("DEBUG", 3);
// $f3->set("CORS.origin", "*");

// Initializing error service
$lang = $f3->exists('LANG') ? $f3->get('LANG') : "en";
\Services\Error::instance($f3->get('ERROR_FILE_PATH'), $lang);

// Wrap all system errors with error service
$f3->set("ONERROR", function ($data) {
    \Services\Error::instance()->throwError([
        "errcode" => 0,
        "status" => $data["ERROR"]["code"],
        "message" => $data["ERROR"]["text"]
    ], true);
});

// Routing
$f3->map('/document', '\Controllers\Documents');
$f3->map('/document/@id', '\Controllers\DocumentSingle');
$f3->map('/search', '\Controllers\Search');

$f3->run();
