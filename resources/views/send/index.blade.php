@extends('layouts.app')

<script src="../js/send.js" defer></script>

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			@if (!empty(session('send_error')) || (!empty(session('error_msg'))))
				<div class="card">
					<div class="card-header" style="color:red;">エラー</div>
					<div class="card-body">
						@if (!empty(session('send_error')))
							<span style="color:red;">{{ session('send_error') }}行目に{{ old('import_flg') == 1 ? '電話番号' : '会員ID' }}を指定してください。</span>
						@else
							<span style="color:red;">{{ session('error_msg') }}</span>
						@endif
					</div>
				</div>
				<br /><br />
			@endif
			<form action="{{ route('sendConfirm') }}" method="post" enctype="multipart/form-data">
				@csrf
				<div class="card">
				<div class="card-header">配信登録画面</div>
					<div class="card-body">
						対象テンプレート<br />
						<select name="select_template" class="custom-select  @error('select_template') is-invalid @enderror" id="select_template">
							<option value="">配信登録対象のテンプレートを選択してください</option>
							@foreach ($template_list as $template_list_key => $template_list_value)
								<option value="{{ $template_list_value['id'] }}" {{ !empty(old('select_template')) && old('select_template') == $template_list_value['id'] ? 'selected' : '' }}>{{ $template_list_value['template_name'] }}</option>
							@endforeach
						</select>
						@error('select_template')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
					</div>
					<div class="card-body">
						配信元事業部：<span id="type"></span><br />
						配信種別：<span id="send_type"></span><br />
						<input type="hidden" name="type" value="" />
						<input type="hidden" name="send_type" value="" />
					</div>

					<div class="card-body" id="title_div">
						件名<br />
						<input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}" readonly />
					</div>
					<div class="card-body">
						本文<br />
						<textarea name="text" id="text" style="height:300px;resize:none;" class="form-control" readonly></textarea>
						<br />
						文字数：<span id="strNum">0</span><span style="display:none;color:red;margin-left:10px" id="warning_str">{{ Config::get('const.sms_warning_msg') }}</span>
						<input type="hidden" name="str_num">
					</div>
				</div>
				<br><br>

				<div class="card">
					<div class="card-header">CSV取り込み</div>
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
				</div>
				<br /><br />
				<div class="card" id="send_custom">
					<div class="card-header">配信登録カスタマイズ</div>
					<div class="card-body">
						<input type="checkbox" name="send_required" class="custom-checkbox" {{ empty(old('send_required')) ? '' : 'checked' }}> 配信拒否者に送付しない<br />
						（会員番号を選択している場合のみ。データベースに正しいデータが登録されている時のみ有効です。）<br />
						※よくわからない場合はチェックを入れておいてください。
						<br /><br />
					</div>
				</div>
				<br />
				<div class="d-flex justify-content-center">
					<button type="button" onclick="clear_button(0)" class="btn btn-outline-secondary" style="margin-right:10px">内容をクリア</button>
					<button type="subimit" class="btn btn-outline-primary">確認</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
