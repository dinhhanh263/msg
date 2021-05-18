$(function(){
	// 画面読み込み完了後
	$(function () {
		radioSendRequired();
		importMethodChoice();
	});

	// 取り込み方法が電話番号の場合、配信拒否者チェックボックスは選択できないようにする
	$('input[name="import_flg"]').change( function(){
		radioSendRequired();
	});

	// 取り込み方法
	$('input[name="import_method"]').change( function(){
		importMethodChoice();
	});

});

function radioSendRequired()
{
	if ($('input[name="import_flg"]:checked').val() == 1) {
		// 電話番号が選択されていた場合
		$('#send_custom').css('display', 'none');
		$('input[name="send_required"').prop('checked', false);
	} else {
		// 会員IDが選択されていた場合
		$('#send_custom').css('display', 'block');
	}
}

// 対象取り込み方法
function importMethodChoice() {
	var now_import_method = $('input[name="import_method"]:checked').val();

	if (now_import_method == 1) {
		// CSVから取り込む場合
		$('#csv_method').css('display', '');
		$('#one_send_method').css('display', 'none');
		$('#send_custom').css('display', 'none');
	} else {
		// 1件のみ配信登録（会員番号）
		$('#csv_method').css('display', 'none');
		$('#one_send_method').css('display', '');
		$('#send_custom').css('display', 'block');
	}
}
