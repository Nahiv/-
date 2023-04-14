<?php
function connectDB() {
    try {
        $pdo = new PDO('mysql:dbname='.DB_NAME.';host='.DB_HOST, DB_USER, DB_PASS);
        return $pdo;
    } catch (PDOException $e) {
        exit($e->getMEssage());
    }
}

function fileSave($save_filename, $save_path, $start_datetime) {
    $result = False;

    $sql = "INSERT INTO images (image_name, image_path, start_datetime) VALUE (?,?,?)";

    try {
        $stmt = connectDB()->prepare($sql);
        $stmt->bindValue(1, $save_filename);
        $stmt->bindValue(2, $save_path);
        $stmt->bindValue(3, $start_datetime);
        $result = $stmt->execute();
        return $result;
    } catch (\Exception $e) {
        echo $e->getMessage();
        return $result;
    }

}

function getAllFile() {
    $sql = "SELECT * FROM images";
    $fileDate = connectDB()->query($sql);
    return $fileDate;
}

//日付から予定を取得する
function getSchedulesByData($pdo, $date) {
    $sql = 'SELECT * FROM schedules WHERE CAST(start_datetime AS DATE) = :start_datetime ORDER BY start_datetime ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':start_datetime', $date, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetchAll();//実行結果を受けるためにreturnで返す
}

function h($string) {
    return htmlspecialchars($string, ENT_QUOTES);
}

?>