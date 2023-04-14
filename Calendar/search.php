<?php
require_once('config.php');
require_once('functions.php');

$title = "予定を検索 | ".SITE_NAME;

//配列を用意して、GETパラメータに値がある場合$whereに条件を追加
$where = [];
$params = [];
$start_date = '';
$end_date = '';
$keyword = '';
//パラメータをチェック
if (!empty($_GET['start_date'])) {
    $start_date = $_GET['start_date'];//入力した検索条件表示
    $where[] = 'CAST(start_datetime AS DATE) >= :start_date';
    $params[':start_date'] = $start_date;//プレースホルダーをキーにして値をセット
}
if (!empty($_GET['end_date'])) {
    $end_date = $_GET['end_date'];
    $where[] = 'CAST(start_datetime AS DATE) <= :end_date';
    $params[':end_date'] = $end_date;
}
if (!empty($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
    $where[] = 'task LIKE :task';
    $params[':task'] = '%'.$keyword.'%';
}
//$where配列が空でないかをチェックして検索するかどうかを判定
// if(!empty($_GET))でも同じ結果だが、条件が追加できるかどうかを確認するため
if (!empty($where)) {
    //$where配列の要素をANDで繋ぐ
    //implode関数 → 配列の要素を大文字で連結することができる(ANDで繋ぐため)
    $where = implode(' AND ', $where);

    $pdo = connectDB();
    //文字列にした$whereを使ってSQL文を作成
    $sql = 'SELECT * FROM schedules WHERE ' . $where . ' ORDER BY start_datetime ASC';
     $stmt = $pdo->prepare($sql);
    //foreachでbindValueメソッドを実行
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val, PDO::PARAM_STR);
    }

    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

}

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
            <div class="col-lg-8 offset-lg-2"></div>
            <h4 class="text-center">イベントを検索</h4>

            <form class="row row-cols-lg-auto g-2 align-items-center">
                <div class="col-12 dp-parent">
                    <label class="visually-hidden "for="inputStartDate">開始日時</label>
                    <input type="text" name="start_date" id="inputStartDateTime" class="form-control search-data"
                     placeholder="開始日" value="<?= h($start_date); ?>">
                </div>

                <div class="col-12 dp-parent">
                    <label class="visually-hidden" for="inputEndDate">終了日時</label>
                    <input type="text" name="end_date" id="inlineFormInputGroupUsername" class="form-control seach-date" 
                     placeholder="終了日" value="<?= h($end_date); ?>">
                </div>

                <div class="col-12">
                    <label class="visually-hidden" for="inputTask">キーワード</label>
                    <input type="text" name="keyword" id="inputTask" class="form-control" 
                     placeholder="キーワード" value="<?= h($keyword); ?>">
                </div>

                <div class="col-12 d-grid">
                    <button type="submit" class="btn btn-success">検索</button>
                </div>
            </form>

            <?php if (!empty($where)): ?>
                <h6 class="mt-5">検索結果:<?= count($rows); ?>件</h6>
                <?php if (count($rows) > 0):?>
                    <table class="table mt-4">
                        <thead>
                            <tr>
                                <th style="width: 20%;">開始日時</th>
                                <th style="width: 20%;">終了日時</th>
                                <th>予定</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?= date('Y/n/j H:i', strtotime($row['start_datetime'])); ?></td>
                                    <td><?= date('Y/n/j H:i', strtotime($row['end_datetime'])); ?></td>
                                    <td><a href="detail.php?ymd=<?= date('Y-m-d', strtotime($row['start_datetime'])); ?>"><?= h($row['task']); ?></a></td>
                                </tr>
                            <?php endforeach; ?>    
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-danger mt-4" role="alert">
                        イベントが見つかりませんでした。
                    </div>
                <?php endif; ?>
                <div class="mt-4"><a href="search.php" class="btn btn-sm btn-link" role="button">検索条件をクリア</a></div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once('elements/footer.php'); ?>

</body>
</html>

