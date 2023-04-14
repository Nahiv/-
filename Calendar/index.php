<?php
require_once('config.php');
require_once('functions.php');
$title = SITE_NAME;
//前月・次月リンクが押された場合は、パラメータから年月を取得
if (isset($_GET['ym'])) {
    $ym = $_GET['ym'];
} else {
    $ym = date('Y-m'); //今月の年月を表示
}
//タイムスタンプを作成→フォーマットチェック
$timestamp = strtotime($ym . '-01');
if ($timestamp === false) {
    $ym = date('Y-m');
    $timestamp = strtotime($ym. '-01');
}
//該当月の日数取得
$day_count = date('t', $timestamp);
//1日が何曜日か
$youbi = date('w', $timestamp);
//カレンダーのタイトルを作成
$html_title = date('Y年n月', $timestamp);
//前月・次月取得
$prev = date('Y-m', strtotime('-1 month', $timestamp));
$next = date('Y-m', strtotime('+1 month', $timestamp));
//今日の日付
$today = date('Y-m-d');
//カレンダー準備
$weeks = [];
$week =  '';
//１週目：空のセル 
$week .= str_repeat('<td></td>',$youbi);
//データ接続
$pdo = connectDB();

//祝日リストを取得
$sql = 'SELECT holiday_date, holiday_name FROM holidays WHERE YEAR(holiday_date) = :year AND MONTH(holiday_date) = :month';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':year', date('Y', strtotime($ym)), PDO::PARAM_STR);
$stmt->bindValue(':month', date('m', strtotime($ym)), PDO::PARAM_STR);
$stmt->execute();
$holidayList = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);


for ($day = 1; $day <= $day_count; $day++, $youbi++) {

    $date = $ym .'-'. sprintf('%02d', $day);
    //予定を取得
    $rows = getSchedulesByData($pdo, $date);

    //今日の日付
    if ($date == $today) {
        $week .= '<td class="today">';
    } else {
        $week .= '<td>';
    }

    $week .= '<a href="detail.php?ymd='. $date .'">';
    //holidayList配列　key(日付) => 名前　なので, !emptyで祝日かどうかを判定 
    if (!empty($holidayList[$date])) {
        $week .= '<span class="text-danger">'. $day .'</span>';
        $week .= '<span class="text-danger fw-bolder d-none d-md-inline holiday-name">'. $holidayList[$date] .'</span>';
    } else {
        $week .= $day;
    }

    if (!empty($rows)) {
        $week .= '<div class="badges">';
            foreach ($rows as $row) {
                $task = date('H:i', strtotime($row['start_datetime'])).''.h($row['task']);
                $week .= '<span class="badge text-wrap ' .$row['color']. '">' .$task. '</span>';
            }
            $week .= '</div>';

    }
    $week .= '</a></td>';

    //日曜または最終日の場合
    if ($youbi % 7 == 6 || $day == $day_count) {
        
        if ($day == $day_count) {
            //月の最終日の場合、空セルを追加
            //例　最終日が金曜日の場合、土・日曜日の空セルを追加
            $week .= str_repeat('<td></td>', 6 - $youbi % 7);
        }
        //weeks配列にtrと$weekを追加する
        $weeks[] = '<tr>'.$week.'</tr>';

        $week = '';
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
        <table class="table table-bordeared calendar"> 
            <thead>
                <tr class="head-cal fs-4">
                    <th colspan="1" class="text-start"><a href="index.php?ym=<?= $prev; ?>">&lt;</a></th>
                    <th colspan="5"><?= $html_title ?></th>
                    <th colspan="1" class="text-end"><a href="index.php?ym=<?= $next; ?>">&gt;</a></th>
                </tr>
                <tr class="head-week">
                    <th>日</th>
                    <th>月</th>
                    <th>火</th>
                    <th>水</th>
                    <th>木</th>
                    <th>金</th>
                    <th>土</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($weeks as $week) {echo $week;}?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once('elements/footer.php'); ?>

</body>

</html>

