function copy_button(param) {
	// テキストエリアを用意する
	var copyFrom = document.createElement("textarea");
	// テキストエリアへ値をセット
	copyFrom.textContent = param;

	// bodyタグの要素を取得
	var bodyElm = document.getElementsByTagName("body")[0];
	// 子要素にテキストエリアを配置
	bodyElm.appendChild(copyFrom);

	// テキストエリアの値を選択
	copyFrom.select();
	// コピーコマンド発行
	var retVal = document.execCommand('copy');
	// 追加テキストエリアを削除
	bodyElm.removeChild(copyFrom);

	alert('【' + param + '】をコピーしました');
}