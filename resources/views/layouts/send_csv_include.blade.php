@if ($ary_template[0]['send_type'] == Config::get('const.SEND_TYPE_MAIL'))
	<div class="card-body">
		【CSVの形式について】<br />
		・1列目を会員IDまたはメールアドレスにしてください。<br />
		・拡張子を「.csv」の形にしてください。<br />
		・途中でメールアドレスまたは会員IDが指定されていない場合、CSV読み込み時にエラーとなります。<br/>
		<br />
		<input type="file" value="ファイルを選択" name="csv_file" accept=".csv" class="form-control-file @error('csv_file') is-invalid @enderror" value="{{ old('csv_file') }}">
		@error('csv_file')
			<span class="invalid-feedback" role="alert">
				<strong>{{ $message }}</strong>
			</span>
		@enderror
		<br />
		<input type="checkbox" name="head_line" class="custom-checkbox" {{ empty(old('head_line')) ? '' : 'checked' }}> ヘッダー無し（１行目も登録対象のメールアドレスまたは会員IDが入っている場合にチェックを入れてください。）
		<br /><br />
		・1列目を選択してください。<br />
		<input type="radio" name="import_flg" class="custom-radio" value="1" {{ !empty(old('import_flg')) && old('import_flg') == 1|| empty(old('import_flg')) ? 'checked' : '' }}>　メールアドレス　
		<input type="radio" name="import_flg" class="custom-radio" value="2" {{ !empty(old('import_flg')) && old('import_flg') == 2 ? 'checked' : '' }}>　会員番号
		@error('import_flg')
			<span class="invalid-feedback" role="alert">
				<strong>{{ $message }}</strong>
			</span>
		@enderror
		<br />
		※メールアドレスを選択した場合、強制的に配信登録します。
	</div>
@elseif ($ary_template[0]['send_type'] == Config::get('const.SEND_TYPE_SMS'))
	<div class="card-body">
		【CSVの形式について】<br />
		・1列目を会員IDまたは電話番号にしてください。<br />
		・拡張子を「.csv」の形にしてください。<br />
		・途中で電話番号または会員IDが指定されていない場合、CSV読み込み時にエラーとなります。<br/>
		<br />
		<input type="file" value="ファイルを選択" name="csv_file" accept=".csv" class="form-control-file @error('csv_file') is-invalid @enderror" value="{{ old('csv_file') }}">
		@error('csv_file')
			<span class="invalid-feedback" role="alert">
				<strong>{{ $message }}</strong>
			</span>
		@enderror
		<br />
		<input type="checkbox" name="head_line" class="custom-checkbox" {{ empty(old('head_line')) ? '' : 'checked' }}> ヘッダー無し（１行目も登録対象の電話番号または会員IDが入っている場合にチェックを入れてください。）
		<br /><br />
		・1列目を選択してください。<br />
		<input type="radio" name="import_flg" class="custom-radio" value="1" {{ !empty(old('import_flg')) && old('import_flg') == 1|| empty(old('import_flg')) ? 'checked' : '' }}>　電話番号　
		<input type="radio" name="import_flg" class="custom-radio" value="2" {{ !empty(old('import_flg')) && old('import_flg') == 2 ? 'checked' : '' }}>　会員番号
		@error('import_flg')
			<span class="invalid-feedback" role="alert">
				<strong>{{ $message }}</strong>
			</span>
		@enderror
		<br />
		※電話番号を選択した場合、強制的に配信登録します。
	</div>
@endif