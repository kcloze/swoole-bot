<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) kcloze <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

date_default_timezone_set('Asia/Shanghai');

require __DIR__ . '/../vendor/autoload.php';
// use Kcloze\Bot\Api\Baidu;

// $baidu =new Baidu();
// $rest  = $baidu->search('众泰汽车');

// var_dump($rest);

use Kcloze\Bot\Api\So;
$baidu =new So();
$rest  = $baidu->search('众泰汽车');

var_dump($rest);
