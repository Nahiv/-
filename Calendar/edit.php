<?php 
require_once('config.php');
require_once('functions.php');

$title = "予定の編集 |" .SITE_NAME;

//存在・形式チェック
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location:index.php');
    exit();
}

$schedule_id = $_GET['id'];

$pdo = connectDB();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    //編集データを取得する
    //SELECT * FROMで全てのデータを取得 //WHEREで条件文 
    //schedule_idと一致するデータをschedulesテーブルから１つ取得する条件
    //LIMITで何件のデータを取得するかを指定 
    $sql = 'SELECT * FROM schedules WHERE schedule_id = :schedule_id LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':schedule_id', $schedule_id, PDO::PARAM_INT);
    $stmt->execute();
    //今回は取得するデータが１つなのでfetchAllではなくfetchメソッドを使用
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    //データが見つからなかった場合
    if (empty($row) || $row === false) {
        header('Location:index.php');
        exit();
    }

    $start_datetime = str_replace('', 'T', $row['start_datetime']);
    $end_datetime = str_replace('', 'T', $row['end_datetime']);
    $task = $row['task'];
    $color = $row['color'];
    $err = [];



} else {
    //予定を編集する
    $start_datetime = $_POST['start_datetime'];
    $end_datetime = $_POST['end_datetime'];
    $task = $_POST['task'];
    $color = $_POST['color'];

    if($start_datetime == '') {
        $err['start_datetime'] = '開始日時を入力してください。';
    }

    if($end_datetime == '') {
        $err['end_datetime'] ='終了日時を入力してください。';
    }   

    if($task  == '') {
        $err['task'] = '予定を入力してください。';
    } else if (mb_strlen($task, 'UTF-8') > 128) {
        $err['task'] = '128文字以内で入力してください。';
    }

    if($color == '') {
        $err['color'] = 'カラーを選択してください。';
    }

    if(empty($err)) {
        $sql = 'UPDATE schedules
                SET start_datetime = :start_datetime, end_datetime = :end_datetime, task = :task, color = :color, modified_at = now()
                WHERE schedule_id = :schedule_id';

        //SQLを実行する準備
        $stmt = $pdo->prepare($sql);
        //値をリセット
        $stmt->bindValue(':start_datetime', $start_datetime, PDO::PARAM_STR);
        $stmt->bindValue(':end_datetime', $end_datetime, PDO::PARAM_STR);
        $stmt->bindValue(':task', $task, PDO::PARAM_STR);
        $stmt->bindValue(':color', $color, PDO::PARAM_STR);
        $stmt->bindValue(':schedule_id', $schedule_id, PDO::PARAM_INT);
        //ステートメントを実行
        $stmt->execute();

        /*メッセージの作成、変数の初期化
        $success_msg = date('Y年m月d日', strtotime($start_datetime)).'の予定を追加しました。';
        $start_datetime = '';
        $end_datetime = '';
        $task = '';
        $color = '';
        */

        //予定詳細画面に遷移
        header('Location:detail.php?ymd='.date('Y-m-d', strtotime($start_datetime)));
        exit();
    }
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
        <div class="col-lg-6 offset-lg-3">
            <h4 class="text-center">予定の編集</h4>
            <form method="post" novalidate>
                <div class="mb-4 dp-parent">
                    <label for="inputStartDateTime" class="form-label">開始日時</label>
                    <input type="text" name="start_datetime"  id="inputStarDateTime"
                        class="form-control task-datetime <?php if(!empty($err['start_datetime'])) echo 'is-invalid'; ?>"
                        placeholder="開始日時を選択してください。" value="<?=h($start_datetime);?>">
                        <?php if(!empty($err['start_datetime'])): ?>
                            <div id="ipnputStartDateTimeFeeback" class="invalid-feedback">
                                *<?= $err['start_datetime']; ?>
                            </div>
                        <?php endif; ?>
                </div>

                <div class="mb-4 dp-parent">
                    <label for="inputEndDateTime" class="form-label">終了日時</label>
                    <input type="text" name="end_datetime" id="inputEndDateTime" 
                    class="form-control task-datetime <?php if(!empty($err['end_datetime'])) echo 'is-invalid'; ?>"
                        placeholder="終了日時を選択してください。" value="<?=h($end_datetime);?>">
                        <?php if(!empty($err['end_datetime'])): ?>
                            <div id="inpuptEndDatetimeFeedback" class="invalid-feedback">
                                *<?= $err['end_datetime']; ?>
                            </div>
                        <?php endif; ?>
                </div>

                <div class="mb-4 dp-parent">
                    <label for="inputTask" class="form-label">更新</label>
                    <input type="text" name="task" id="inputTask" class="form-control <?php if (!empty($err['task'])) echo 'is-invalid'; ?>" 
                     placeholder="予定を入力してください。" value="<?=h($task);?>">
                    <?php if(!empty($err['task'])): ?>
                        <div id="inputTaskFeedback" class="invalid-feedback">
                            *<?=$err['task']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-5">
                    <label for="selectColor" class="form-label">カラー</label>
                    <select name="color" id="selectColor" class="form-select <?= $color; ?> <?php if (!empty($err['color'])) echo "is-invalid;" ?>">
                         <?php foreach(COLOR_LIST as $key => $val):?>
                            <option value="<?= $key;?>" <?php if($color == $key) echo 'selected';?>> <?= $val ?> </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($err['color'])): ?>
                        <div id="selectColorFeedback" class="invalid-feedback">
                            *<?=$err['color']; ?>
                        </div>
                    <?php endif; ?>
                </div>
                    
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">登録</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once('elements/footer.php'); ?>

</body>
</html>

