<script>
$function () {
    var ua = navigator.userAgent;//端末・ブラウザ情報を取得
    //indexOf(キーワード)で、文字列の何文字目にキーワードが現れるか
    //見つからなかった場合ｰ１が帰ってくる
    if ((ua.indexOf('iPhone') > 0 || ua.indexOf('Andoroid') > 0) 
        && ua.indexOf('Mobile') > 0) {
            $('input[name='ym']').removeAttr('id').attr('type', 'month');
            //name属性値がymのinputタグに対して　id属性を外して　type属性をmonthにする
            $('input[name= 'start_datetime']').removeClass('task-datetime').attr('type', 'datetime-local');
            $('input[name= 'end_datetime']').removeClass('task-datetime').attr('type', 'datetime-local');
            $('input[name= 'start_date'],input[name= 'end_date']').removeClass('search-date').attr('type', 'date');
            $('.visually-hidden').rewmoveClass('visually-hidden').addClass('form-label');
        } else {
            $('.sp-label').remove();
        }
        //ラベルの表示・非表示
        $('input[name="ym"]').focus(function() {
            //フォーカスした時
            $('.sp-label').hide();
        }).blur(function() {
            //フォーカスを外した時
            if (!$(this).val()) {
                $('.sp-label').show();
            }
        });
}

</script>