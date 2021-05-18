
// もっと見る（send_no）
function send_no_more($param)
{
	var target_name = '#more_send_' + $param;

	// 現在表示されている個数
	var last_num = $(target_name + ' tr').filter(':visible').length;

	// 5件表示する
	for (i = last_num+1;i <= last_num+5; i++) {
		// 表示処理
		$(target_name + i).show('slow');
	}

	// 非表示の要素の個数
	var disp_none_num = $(target_name + ' tr').filter(':hidden').length;

	if (disp_none_num == 0) {
		$(target_name + '_button').css('display', 'none');
	}
}