<?php
date_default_timezone_set('Asia/Shanghai');

require __DIR__ . '/vendor/autoload.php';
$config = require_once __DIR__ . '/config.php';

$console = new Kcloze\Bot\Console($config);
$console->run();
