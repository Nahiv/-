<?php

date_default_timezone_set('Asia/Tokyo');

define('SITE_NAME', 'Calendar');
define('DB_HOST', 'localhost');
define('DB_NAME', 'calendar');
define('DB_USER', 'root');
define('DB_PASS', '');

$colorList = [
    'bg-light' => 'デフォルト',
    'bg-danger' => '赤',
    'bg-warning' => 'オレンジ',
    'bg-primary' => '青',
    'bg-info' => '水色',
    'bg-success' => '緑',
    'bg-dark' => '黒',
    'bg-secondary' => 'グレー'
];
define('COLOR_LIST', $colorList);
?>