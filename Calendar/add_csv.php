<?php 
require_once('config.php');
require_once('functions.php');
//祝日のデータを読み込み
$csv = file_get_contents('https://www8.cao.go.jp/chosei/shukujitsu/syukujitsu.csv');
//文字コードを変更
$csv = mb_convert_encoding($csv, 'UTF-8', 'SJIS-win');

$tmp = tmpfile();//一時ファイルを作成
fwrite($tmp, $csv);//一時的に作成した$tmpに$csvを書き込み
rewind($tmp);//ファイルポインタが一番最後にある状態なので先頭に戻す

//一行ずつ取り出して配列を作成
$rows = [];
while(($data = fgetcsv($tmp)) !== FALSE) {
    $rows[] = $data;
}
//先頭の要素を１つ削除
array_shift($rows);
//ファイルを閉じる
fclose($tmp);
//データーベースに保存
$pdo = connectDB();
foreach ($rows as $row) {
    //日付か名前のどちらかが未入力の場合はスキップ
    if (empty($row[0]) || empty($row[1])) {
        continue;
    }
    
    $sql ='INSERT INTO holidays(holiday_date, holiday_name, created_at, modified_at)
    	 VALUES(:holiday_date, :holiday_name, now(),now())';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':holiday_date', $row[0], PDO::PARAM_STR);
    $stmt->bindValue(':holiday_name', $row[1], PDO::PARAM_STR);
    $stmt->execute();
}

?>