<?php

define('APP_PATH', dirname(dirname(__FILE__)) . '/protected');
define('V_PATH', dirname(APP_PATH).'/framework');
define('V_DEBUG', false);

if (!is_file(V_PATH.'/V.php')) {
    echo 'Please move "framework" to this directory.';
    exit;
}

try {
    require_once V_PATH . '/V.php';
    V::loadBootstrap('web', 'main')->run();
} catch (Exception $e) {
    echo defined('V_DEBUG') && V_DEBUG ? $e->getMessage() : 'Error';
}