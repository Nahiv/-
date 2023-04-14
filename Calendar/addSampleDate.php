<?php
require_once('config.php');
require_once('functions.php');

// ①と②を設定してください。
// ①何年何月に予定を追加するか
$ym = '2022-06';
// ②何件の予定を追加するか
$num = 20;


// -----以下は変更不要です。------
$max = date('t', strtotime($ym . '-01'));
$start = strtotime($ym . '-01');
$end = strtotime($ym . '-' . $max);
$taskList = [
    '●●に行く',
    '●●さんに電話',
    '●●を確認',
    '●●会社',
    '▲▲を予約',
    '▲▲さんにメール',
    '▲▲を買う',
    '▲▲で会議',
    '■■を注文',
    '■■を勉強',
    '■■旅行',
    '■■試験',
];
$colorList = array_keys(COLOR_LIST);
$pdo = connectDB();

for ($i=0; $i < $num; $i++) {
    $timestamp = mt_rand($start, $end);
    $minute = mt_rand(10, 90);
    $start_datetime = date('Y-m-d H:i', $timestamp);
    $end_datetime = date('Y-m-d H:i', strtotime('+ ' . $minute . 'minutes', $timestamp));

    shuffle($taskList);
    shuffle($colorList);
    $task = $taskList[0];
    $color = $colorList[0];

    $sql = 'INSERT INTO schedules(start_datetime, end_datetime, task, color, created_at, modified_at)
    VALUES(:start_datetime, :end_datetime, :task, :color, now(), now())';

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':start_datetime', $start_datetime, PDO::PARAM_STR);
    $stmt->bindValue(':end_datetime', $end_datetime, PDO::PARAM_STR);
    $stmt->bindValue(':task', $task, PDO::PARAM_STR);
    $stmt->bindValue(':color', $color, PDO::PARAM_STR);
    $stmt->execute();
}
?>