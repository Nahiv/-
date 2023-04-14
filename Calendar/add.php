<?php 
require_once('config.php');
require_once('functions.php');
$title = '予定の追加 |'.SITE_NAME;

//エラーメッセージを入れる配列を用意
$err = [];
$start_datetime = '';
$task = '';
$color = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $start_datetime = $_POST['start_datetime'];
    $end_datetime = $_POST['end_datetime'];
    $task = $_POST['task'];
    $color = $_POST['color'];

    if($start_datetime == '') {
        $err['start_datetime'] = '開始日時を入力してください。';
    }

    if($task  == '') {
        $err['task'] = '内容を入力してください。';
    } else if (mb_strlen($task, 'UTF-8') > 128) {
        $err['task'] = '128文字以内で入力してください。';
    }

    if($color == '') {
        $err['color'] = 'カラーを選択してください。';
    }
 //$err関数が空だった場合
    if(empty($err)) {
        //データベース接続
        $pdo = connectDB();
        //SQL文の作成
        $sql = 'INSERT INTO schedules(start_datetime, end_datetime, task, color, created_at, modified_at)
        VALUES(:start_datetime, :end_datetime, :task, :color, now(), now())';
        //SQLを実行する準備
        $stmt = $pdo->prepare($sql);
        //値をリセット
        $stmt->bindValue(':start_datetime', $start_datetime, PDO::PARAM_STR);
        $stmt->bindValue(':end_datetime', $end_datetime, PDO::PARAM_STR);
        $stmt->bindValue(':task', $task, PDO::PARAM_STR);
        $stmt->bindValue(':color', $color, PDO::PARAM_STR);
        //ステートメントを実行
        $stmt->execute();

        $files = $_FILES['image'];

        // 複数ファイルが取得できるため、繰り返し処理で1ファイルずつ処理をします
        for ($i = 0; $i < count($files['name']); $i++) {
            // ファイルのバリデーション
            $file_err = $files['error'][$i];
            $filename = basename($files['name'][$i]);
            $err_msgs = array();
        
            // 拡張は画像形式か
            $allow_ext = array('jpg', 'jpeg', 'png');
            $file_ext = pathinfo($filename, PATHINFO_EXTENSION);
            if (!in_array(strtolower($file_ext), $allow_ext)) {
                array_push($err_msgs, '画像ファイルを添付してください。');
            }
            // ファイルパスの生成
            $upload_dir = 'images/';
            $tmp_path = $files['tmp_name'][$i];
            $save_filename = date('YmdHis') . $filename;
            $save_path = $upload_dir . $save_filename;
        
            // ファイルの保存処理
            if (count($err_msgs) === 0) {
                // ファイルはあるかどうか？
                if (is_uploaded_file($tmp_path)) {
                    if (move_uploaded_file($tmp_path, $save_path)) {
                        echo $filename . 'を' . $upload_dir . 'アップしました。<br>';
                        // DBに保存(ファイル名、ファイルパス)

                        $result = fileSave($save_filename, $save_path, $start_datetime);
                    }
                 }
            }
        
        }
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
            <h4 class="text-center">イベント追加</h4>
            <form action="" method="post" novalidate enctype="multipart/form-data">
                <div class="mb-4 dp-parent">
                    <label for="inputStartDateTime" class="form-label">開始日時</label>
                    <input type="text" name="start_datetime"  id="inputStarDateTime"
                        class="form-control task-datetime <?php if(!empty($err['start_datetime'])) echo 'is-invalid'; ?>"
                        placeholder="開始日時を選択してください。" value="<?=h($start_datetime);?>">
                        <?php if(!empty($err['start_datetime'])): ?>
                            <div id="ipnputStartDateTimeFeedback" class="invalid-feedback">
                                *<?= $err['start_datetime']; ?>
                            </div>
                        <?php endif; ?>
                </div>

                <div class="mb-4 dp-parent">
                    <label for="inputEndDateTime" class="form-label">終了日時</label>
                    <input type="text" name="end_datetime" id="inputEndDateTime" 
                    class="form-control task-datetime"
                        placeholder="終了日時があれば選択してください。">
                </div>

                <div class="mb-4 dp-parent">
                    <label for="inputTask" class="form-label">イベント</label>
                    <input type="text" name="task" id="inputTask" class="form-control <?php if (!empty($err['task'])) echo 'is-invalid'; ?>" placeholder="内容を入力してください。" value="<?=h($task);?>">
                    <?php if(!empty($err['task'])): ?>
                        <div id="inputTaskFeedback" class="invalid-feedback">
                            *<?=$err['task']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
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

                    <div class="mb-5 form-group">
                            <label for="inputFile" class="form-label">画像を選択</label>
                                <div class="input-group">
                                    <input type="file"  name="image[]" accept="image/*" class="form-control" id="image" multiple="multiple">
                                    <button type="reset" class="btn btn-outline-secondary reset"><i class="fas fa-times fa-fw"></i>取消</button>
                                </div>
                    </div>
                    
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary" >登録</button>
                </div>
            </form>
        </div>
    </div>


    
</main>

<?php require_once('elements/footer.php'); ?>

</body>
</html>

