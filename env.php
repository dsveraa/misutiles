<?php

if (file_exists(__DIR__ . "/.env")) {
    $vars = parse_ini_file(__DIR__ . "/.env", false, INI_SCANNER_TYPED);

    foreach ($vars as $key => $value) {
        putenv("$key=$value");
    }
}
