<?php
require_once('config.php');
require_once('functions.php');

//存在・形式チェック
if (!isset($_GET['id1'])  || !is_numeric($_GET['id1'])) { //numericは値が数値か判定
    header('Location:index.php');
    exit();
}
//データベース接続
$pdo = connectDB();

$sql = 'DELETE FROM schedules WHERE schedule_id = :schedule_id';
$stmt = $pdo->prepare($sql);
//schedule_id → int, bindValeメソッドの第三引数 → INT
$stmt->bindValue(':schedule_id', $_GET['id1'], PDO::PARAM_INT);
$stmt->execute(); 

$targetDirectory = 'images/';

$sql ='SELECT image_name FROM images WHERE image_id = :image_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':image_id', $_GET['id2'], PDO::PARAM_INT);
$stmt->execute();
$getImageName = $stmt->fetch();

$deleteImageName = unlink($targetDirectory . $getImageName['image_name']);

if ($deleteImage) {
    $sql = 'DELETE FROM images WHERE image_id = :image_id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':image_id', $_GET['id2'], PDO::PARAM_INT);
    $stmt->execute();
}

//$_SEVER['HTTP_REFERER']で１つ前に表示したページのURL
header('Location:' .$_SERVER['HTTP_REFERER']);
exit();

?>