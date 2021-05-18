<div class="card" id="custom_insert">
	<div class="card-header">カスタマイズ</div>
	<div class="card-body">
		本文内容への差し込み項目を設定することができます。<br />
		使用する場合は下記置き換え文字を本文に入れてください。<br />
		<font color="red">
			<span class="mail_csv">
				※差し込み設定可能数：10件<br />
				※1つの置き換え文字列を本文に複数差し込むことはできますが、置き換え文字列の合計が10件までしか指定できません。<br />
			</span>
			<span class="sms_csv">
				※差し込み設定可能数：5件<br />
				※1つの置き換え文字列を本文に複数差し込むことはできますが、置き換え文字列の合計が5件までしか指定できません。<br />
			</span>
			※下記以外の置き換え文字を指定した場合は配信登録時にエラーとなりますので、ご注意ください。<br />
		</font>
		<br />

		<!-- csvから差し込み -->
		CSV置き換え文字
		<table class="table" id="import_csv">
			<thead class="thead-dark">
				<th>置き換え文字列</th>
				<th>csv取得列</th>
			</thead>
			<tbody>
				<tr>
					<td>@{{insertion_item1}}</td>
					<td>csv2列目</td>
				</tr>
				<tr>
					<td>@{{insertion_item2}}</td>
					<td>csv3列目</td>
				</tr>
				<tr>
					<td>@{{insertion_item3}}</td>
					<td>csv4列目</td>
				</tr>
				<tr>
					<td>@{{insertion_item4}}</td>
					<td>csv5列目</td>
				</tr>
				<tr>
					<td>@{{insertion_item5}}</td>
					<td>csv6列目</td>
				</tr>
				<tr class="insert_mail">
					<td>@{{insertion_item6}}</td>
					<td>csv7列目</td>
				</tr>
				<tr class="insert_mail">
					<td>@{{insertion_item7}}</td>
					<td>csv8列目</td>
				</tr>
				<tr class="insert_mail">
					<td>@{{insertion_item8}}</td>
					<td>csv9列目</td>
				</tr>
				<tr class="insert_mail">
					<td>@{{insertion_item9}}</td>
					<td>csv10列目</td>
				</tr>
				<tr class="insert_mail">
					<td>@{{insertion_item10}}</td>
					<td>csv11列目</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<br /><br />