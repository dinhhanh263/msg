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
				<input type="hidden" name="select_template" value="{{ $ary_template[0]['id'] }}" />
				<input type="hidden" name="type" value="{{ $ary_template[0]['type'] }}" />
				<input type="hidden" name="send_type" value="{{ $ary_template[0]['send_type'] }}" />
				<input type="hidden" name="text" value="{{ $ary_template[0]['text'] }}" />
				<div class="card">
				<div class="card-header">配信登録画面</div>
					<div class="card-body">
						配信対象テンプレート名：{{ $ary_template[0]['template_name'] }}<br />
						配信元事業部：{{ Config::get('const.type.' . $ary_template[0]['type']) }}<br />
						配信種別：{{ Config::get('const.send_type.' . $ary_template[0]['send_type']) }}<br />
					</div>

					@if ($ary_template[0]['send_type'] == Config::get('const.SEND_TYPE_MAIL'))
						<div class="card-body" id="title_div">
							件名<br />
							<input type="text" name="title" class="form-control" id="title" value="{{ $ary_template[0]['title'] }}" readonly />
						</div>
					@endif
					<div class="card-body">
						本文<br />
						<textarea name="text" id="text" style="height:300px;resize:none;" class="form-control" readonly>{{ $ary_template[0]['text'] }}</textarea>

						@if ($ary_template[0]['send_type'] == Config::get('const.SEND_TYPE_SMS'))
							<br />
							文字数：<span id="strNum">{{ $ary_template[0]['str_num'] }}</span><span style="display:none;color:red;margin-left:10px" id="warning_str">{{ Config::get('const.sms_warning_msg') }}</span><br />
							<input type="hidden" name="str_num" value="{{ $ary_template[0]['str_num'] }}">
							<font color="red">※置き換え文字を抜いた文字数を表示しています。置き換え後の文字列を含め、文字数が70文字以上の場合追加料金が発生することがありますのでご注意ください。</font>
						@endif
					</div>
				</div>
				<br><br>

				<div class="card">
					<div class="card-header">対象取り込み方法</div>
					<div class="card-body">
						<input type="radio" name="import_method" class="custom-radio" value="1" {{ !empty(old('import_method')) && old('import_method') == 1|| empty(old('import_method')) ? 'checked' : '' }}>　CSVから取り込む　
						<!--
						@if ($ary_template[0]['send_type'] == Config::get('const.SEND_TYPE_SMS'))
							<input type="radio" name="import_method" class="custom-radio" value="2" {{ !empty(old('import_method')) && old('import_method') == 2 ? 'checked' : '' }}>　1件のみ配信登録（会員番号）
						@endif
						-->
					</div>
				</div>
				<br /><br />

				<div class="card" id="csv_method">
					<div class="card-header">CSV取り込み</div>
					@include('layouts.send_csv_include')
				</div>

				<div class="card" id="one_send_method" style="display:none">
					<div class="card-header">1通のみ配信登録</div>
					<div class="card-body">
						会員番号を入力してください。
						<input type="text" name="customer_no" class="form-control @error('customer_no') is-invalid @enderror" value="{{ old('customer_no') }}">
						@error('customer_no')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
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
					<button type="subimit" class="btn btn-outline-primary">確認</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
