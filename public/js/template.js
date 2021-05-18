$(function(){
	// 画面読み込み時
	$(document).ready(function(){
		changeDisp();
	});

	// 読み込み完了後
	$(function () {
		import_customize();

		// getパラメータにlabel_idが設定されている場合
		var get_param = location.search.substring(1).split('&');
		if (get_param != '') {
			$('input[name=url_type]:eq(0)').prop('checked', true);
			$('#template_regist').prop('disabled', true);
		}

		url_type_disp();
		getStr();
	});

	// 複製元テンプレートプルダウンが選択された時の処理
	$('#copy_template').change(
		function() {
			// 選択されているテンプレートidを取得する
			var template_id = $('#copy_template').val();

			$.ajaxSetup({
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
			});

			$.get({
				method		: 'POST',
				url			: '/template/copylist',
				dataType	: 'json',
				data		: {
					'id' : template_id
				},
			}).done(function (template_data) {
				// 成功した場合
				// 値をセットする
				$('#title').val(template_data[0]['title']);			// 件名
				$('#text').val(template_data[0]['text']);			// 本文
				$('#type').val(template_data[0]['type']);			// 送信元タイプ
				$('#send_type').val(template_data[0]['send_type']);	// 送信種別
				$('#url').val(template_data[0]['url']);				// URL

				// 文字数カウント
				getStr();

				// 件名表示非表示
				changeDisp();
			}).fail(function () {
				// 失敗した場合
				alert('複製元テンプレート取得に失敗しました。');
			});
		});

	// 文字数カウント
	$('#text').on("keydown keyup keypress change", function() {
		getStr();
	});

	// urlが入力された場合
	$('#url_input').on("keydown keyup keypress change", function() {
		getStr();
		button_juge(2);
	});

	$('#url_choice').change(
		function() {
			getStr();
			button_juge(1);
		}
	);

	// 表示非表示
	$('#send_type').change( function() {
		changeDisp();
	});

	// カスタマイズ
	$('input[name="import_type"]').change( function() {
		import_customize();
	});

	// 配信種別を選択した場合
	$('#send_type').change(
		function() {
			// submit_flgを変更
			$('input[name="submit_flg"]').val('2');

			$('form').attr('action', '/template/edit');
			$('form').submit();
	});

	// 登録ボタンを押下した場合
	$('#template_regist').click(
		function() {
			// submit_flgを変更
			$('input[name="submit_flg"]').val('1');

			// required対策
			if ($('#template_create').find(':invalid').length === 0) {
				$('form').attr('action', '/template/confirm');
				$('form').submit();
			} else {
				$(this).find(':invalid').show();
			}
	});

	// URLのラジオボタンの選択時
	$('input[name="url_type"]').change( function(){
		url_type_disp();
	});

	// リセットボタン押下後
	$('#clear_button').click(function() {
		document.updateForm.reset();
		getStr();
		url_type_disp();
	});
});

function getStr(){
	var text = $('#text').val();

	// 置き換え文字が存在した場合
	for (i = 1; i <= 5; i++) {
		text = text.split('{{insertion_item' + i + '}}').join('');
	}

	var len = text.length;

	// send_typeを取得
	var send_type = $('#send_type').val();

	// URLのタイプを取得
	var url_type = $('input[name="url_type"]:checked').val();

	if (url_type == 1) {
		// url_typeが1（アップロードしたファイルを選択する）
		if ($('#url_choice').val() != '') {
			len = len + 23;
		}
	} else if (url_type == 2) {
		if ($('#url_input').val() != '') {
			len = len + 23;
		}
	}

	// smsかつ70文字超えた場合は注意を表示する
	if (send_type == 2 && len >= 70) {
		$('#warning_str').css('display', 'inline');
	} else {
		$('#warning_str').css('display', 'none');
	}

	// smsかつ660文字以上入力した場合は登録できないようにする
	if (send_type == 2 && len > 660) {
		$('#warning_str_seigen').css('display', 'inline');
		$('#template_regist').prop('disabled', true);
	} else {
		$('#warning_str_seigen').css('display', 'none');
	}

	$('#strNum').text(len);
}

function changeDisp(){
	var send_type = $('#send_type').val();

	// メール以外は件名非表示
	if (send_type == 1) {
		// メールの場合
		$('#title-div').css('display', 'inline');		// 件名表示
		$('#str_count').css('display', 'none');			// 文字数カウント非表示
		$('#url_div').css('display', 'none');			// URL非表示
	} else if (send_type == 2){
		// SMSの場合
		$('#title-div').css('display', 'none');			// 件名非表示
		$('#str_count').css('display', 'inline');		// 文字数カウント表示
		$('#url_div').css('display', 'inline');			// URL表示
	} else {
		// 何も選択されていない場合
		$('#title-div').css('display', 'none');			// 件名非表示
		$('#str_count').css('display', 'none');			// 文字数カウント非表示
		$('#url_div').css('display', 'none');			// URL非表示
	}
}

function clear_button() {
	// 配信元事業部を初期化
	$('#type').val('');

	// 配信種別を初期化
	$('#send_type').val('');

	// 複製元テンプレートを初期化
	$('#copy_template').val('');

	// テンプレート名を初期化
	$('input[name="template_name"]').val('');

	// 本文を初期化
	$('#text').val('');

	// URLを初期化
	$('#url').val('');

	// URLを非表示にする
	$('#url_div').css('display', 'none');

	// 文字数を非表示にする
	$('#str_count').css('display', 'none');

	// カスタマイズを非表示にする
	$('#custom_insert').css('display', 'none');
}

// カスタマイズ機能
function import_customize() {
	// 今の配信種別
	var now_flg = $('#send_type').val();

	if (now_flg == 1) {
		// メールの場合
		$('#custom_insert').css('display', '');
		$('.insert_mail').css('display', '');
		$('.mail_csv').css('display', '');
		$('.sms_csv').css('display', 'none');
	} else if (now_flg == 2) {
		// SMSの場合
		$('#custom_insert').css('display', '');
		$('.insert_mail').css('display', 'none');
		$('.mail_csv').css('display', 'none');
		$('.sms_csv').css('display', '');
	} else {
		$('#custom_insert').css('display', 'none');
	}
}

// プレビューボタン押下時の処理
// $flg = 1(新規作成) 2(編集)
function preview_button($flg) {
	var now_url_type = $('input[name="url_type"]:checked').val();

	// URLを取得
	if (now_url_type == 1) {
		// ファイルを選択した場合
		// IDを取得
		var now_url_choice = $('#url_choice').val();

		if (now_url_choice != '') {
			// ファイル名を取得
			var file_name = $('#file_' + now_url_choice).val();
			// フルURL
			var url = $('#base_url').val() + file_name;
		} else {
			var url = '';
		}
	} else {
		// URL入力した場合
		var url = $('#url_input').val();
	}

	if (!url.match(/^http|https.*/)) {
		alert('入力されたURLが無効です。');
		return false;
	}

	// inputタグにurlを入れる
	$('#url').val(url);

	// プレビュー画面を開く
	if (url != '') {
		window.open(url, '_blank');
		if ($flg == 1) {
			$('#template_regist').prop('disabled', false);
		} else {
			$('#template_update').prop('disabled', false);
		}
	}
}

// URLのタイプを選択した場合
function url_type_disp() {
	// 現在の値を取得
	var now_url_type = $('input[name="url_type"]:checked').val();

	if (now_url_type != undefined) {
		if (now_url_type == 1) {
			$('#url_input_block').css('display', 'none');
			$('#url_choice_block').css('display', '');

			button_juge(1);

		} else {
			$('#url_input_block').css('display', '');
			$('#url_choice_block').css('display', 'none');

			button_juge(2);
		}

		getStr();
	}
}

// 1:choice/2:input
function button_juge(type) {
	if (type == 1) {
		if ($('#url_choice').val() == '') {
			// 未入力の場合
			if ($('#template_regist').length) {
				$('#template_regist').prop('disabled', false);
			} else {
				$('#template_update').prop('disabled', false);
			}
		} else {
			// 入力されている場合
			if ($('#template_regist').length) {
				$('#template_regist').prop('disabled', true);
			} else {
				$('#template_update').prop('disabled', true);
			}
		}
	} else {
		if ($('#url_input').val() != '') {
			if ($('#template_regist').length) {
				$('#template_regist').prop('disabled', true);
			} else {
				$('#template_update').prop('disabled', true);
			}
		} else {
			if ($('#template_regist').length) {
				$('#template_regist').prop('disabled', false);
			} else {
				$('#template_update').prop('disabled', false);
			}
		}
	}
}