<?php
require_once('config.php');
require_once('functions.php');

//値がセットされているか、タイムスタンプを正しく作成できているか判定
if (!isset($_GET['ymd']) || strtotime($_GET['ymd']) === false) {
    //パラメータが空or無効な文字列
    header('Location:index.php');
    exit();
}

$ymd = $_GET['ymd'];

$ymd_formatted = date('Y年n月j日', strtotime($ymd));
$title = $ymd_formatted . 'の予定 | ' . SITE_NAME;

$pdo = connectDB();
$rows = getSchedulesByData($pdo, $ymd);//pdo:データベース接続情報、ymd:日付

// 画像を取得
$files = getAllFile();


$sql = 'SELECT holiday_name FROM holidays WHERE holiday_date = :holiday_date LIMIT 1';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':holiday_date', $ymd, PDO::PARAM_STR);
$stmt->execute();
$holiday = $stmt->fetchColumn();
//配列せずにカラムだけを取得

?>

<!DOCTYPE html>
<html lang="ja" class="h-100">
<head>
    <?php require_once('elements/head.php'); ?>
</head>

<body class="d-flex flex-column h-100">

    <?php require_once('elements/navbar.php'); ?>

<main>
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                    <h4 class="text-center pb-1"><?= $ymd_formatted; ?></h4>

                    <?php if ($holiday): ?>
                        <div class="text-center text-danger"><?= $holiday ?></div>
                    <?php endif; ?>

                    <?php if(!empty($rows)): ?>
                        <table class="table mt-5">
                            <thead>
                                <tr>
                                    <th style="width: 3%;"></th>
                                    <th style="width: 25%;"><i class="far fa-clock"></i></th>
                                    <th style="width: 50%;"><i class="far fa-list"></i></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($rows as $row): ?>
                                    <?php
                                        $color = str_replace('bg', 'text', $row['color']);
                                        $start = date('H:i', strtotime($row['start_datetime']));
                                        $start_datetime = date('Y-m-d', strtotime($row['start_datetime']));
                                        $end_datetime = date('Y-m-d',strtotime($row['end_datetime']));
                                        
                                        if ($start_datetime == $end_datetime) {
                                            $end = date('H:i', strtotime($row['end_datetime']));
                                        } else { 
                                            $end = date('n/j H:i', strtotime($row['end_datetime']));
                                        }
                                    ?>
                                    <tr>
                                        <td><i class="fas fa-square <?=$color ?>"></i></td>
                                        <td><?= $start; ?> ~ <?= $end; ?></td>
                                        <td><?= h($row['task']); ?></td>
                                        <td></td>
                                    </tr>

                                    <tr>
                                        <?php if(!empty($files)): ?>
                                            <td colspan="3">
                                                <?php foreach($files as $file): ?>
                                                    <?php 
                                                        if ($row['start_datetime'] == $file['start_datetime']) {
                                                            $img = $file['image_path']; 
                                                        } else {
                                                            continue;
                                                        }
                                                    ?>
                                                    <img src="<?php echo "{$img}"; ?>" width="200" height="auto" alt="">
                                                <?php endforeach; ?>
                                            </td>
                                        <?php endif; ?>
                                        <td>
                                            <?php if(empty($file)): ?>
                                                <a href="javascript:void(0);"
                                                    onclick="var ok=confirm('この予定を削除してもよろしいですか？'); if(ok) location.href='delete.php?id1=<?= $row['schedule_id'];?>'" class="btn btn-sm btn-link">削除</a>
                                            <?php else: ?>
                                                <a href="javascript:void(0);"
                                                    onclick="var ok=confirm('この予定を削除してもよろしいですか？'); if(ok) location.href='delete.php?id1=<?= $row['schedule_id'];?> & id2=<?= $file['image_id'];?>'" class="btn btn-sm btn-link">削除</a>
                                            <?php endif; ?>
                                        </td>
                                        
                                    </tr>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-dark mt-5" role="alert">
                            予定がありません。予定の追加は<a href="add.php" class="alert-link">こちら</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once('elements/footer.php'); ?>

</body>
</html>

