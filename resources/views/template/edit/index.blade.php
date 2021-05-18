@extends('layouts.app')

<script src="../js/template.js" defer></script>

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			@if (!empty($message['ERROR']))
				<div class="list-group">
					<span class="list-group-item list-group-item-action list-group-item-danger">{{ $message['ERROR'] }}</span>
				</div>
				<br />
			@endif
			<form method="post" enctype="multipart/form-data" id="template_create">
				@csrf
				<input type="hidden" name="submit_flg" value="">
				<input type="hidden" name="preview_cnt" value="0">
				<div class="card">
					<div class="card-header">配信登録設定</div>
					<div class="card-body">
						配信元事業部
						<select name="type" id="type" class="custom-select @error('type') is-invalid @enderror" required>
							<option value="">-</option>
							@foreach ($type as $type_key => $type_value)
								<option value="{{ $type_key }}" {{ old('type') == $type_key ? 'selected' : '' }}>{{ $type_value }}</option>
							@endforeach
						</select>
						@error('type')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
					</div>
					<div class="card-body">
						配信種別
						<select name="send_type" id="send_type" class="custom-select @error('send_type') is-invalid @enderror" required>
							<option value="">-</option>
							@foreach ($send_type as $send_type_key => $send_type_value)
								<option value="{{ $send_type_key }}" {{ old('send_type') == $send_type_key ? 'selected' : !empty($label_id) && $send_type_key == Config::get('const.SEND_TYPE_SMS') ? 'selected' : '' }}>{{ $send_type_value }}</option>
							@endforeach
						</select>
						@error('send_type')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
					</div>
				</div>
				<br /><br />
				<div class="card">
					<div class="card-header">テンプレート作成</div>
					<div class="card-body">
						複製元テンプレート<br />
						<select name="copy_template" class="custom-select" id="copy_template">
							<option value="">複製する場合は選択してください</option>
							@foreach ($template_list as $template_list_key => $template_list_value)
								<option value="{{ $template_list_value['id'] }}" {{ old('copy_template') == $template_list_value['id'] ? 'selected' : '' }}>{{ $template_list_value['template_name'] }}</option>
							@endforeach
						</select>
					</div>
					<div class="card-body">
						テンプレート名<br />
						<input type="text" name="template_name" class="form-control @error('template_name') is-invalid @enderror" value="{{ old('template_name') }}" required>
						@error('template_name')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
					</div>
					<div class="card-body" id="title-div" style="display:none;">
						件名<br />
						<input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}">
						@error('title')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
					</div>
					<div class="card-body">
						本文　<span class="sms_csv" style="display:none">※SMSを選択した場合、改行は無くなります。</span><br />
						<textarea name="text" id="text" style="height:300px;resize:none;" class="form-control @error('text') is-invalid @enderror" required>{{ old('text') }}</textarea>
						@error('text')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
					</div>
					<div class="card-body" id="url_div" style="display:none;">
						<input type="hidden" name="url" value="" id="url" />
						URL　※URLは一律23文字としてカウントします。URLが有効であるか確認してから入力してください。<br />
						<input type="radio" name="url_type" class="custom-radio" value="1" {{ !empty(old('url_type')) && old('url_type') == 1 || !empty($label_id) ? 'checked' : '' }}>　アップロードしたファイルを選択する
						<input type="radio" name="url_type" class="custom-radio" value="2" {{ !empty(old('url_type')) && old('url_type') == 2 || empty(old('url_type')) ? 'checked' : '' }}>　URLを入力する

						<span id="url_input_block">
							<input type="text" id="url_input" name="url_input" class="form-control @error('url_input') is-invalid @enderror" autocomplete="off" value="{{ empty(old('url_input')) ? 'https://kireimo.jp/' : old('url_input') }}" />
							@error('url_input')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</span>

						<span id="url_choice_block" style="display:none">
							<input type="hidden" id="base_url" value="{{ Config::get('azure.azure_base_url') }}" />
							<select id="url_choice" name="url_choice" class="form-control @error('url_choice') is-invalid @enderror">
								<option value="">リストから選択してください</option>
								@foreach ($url_list as $value)
									<option value="{{ $value['id'] }}" {{ !empty(old('url_choice')) ? 'selected' : !empty($label_id) && $label_id == $value['id'] ? 'selected' : '' }}>{{ $value['label'] }}</option>
								@endforeach
							</select>
							@foreach ($url_list as $value)
									<input type="hidden" id="file_{{ $value['id'] }}" value="{{ $value['file_name'] }}" />
								@endforeach
							@error('url_choice')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</span>
						<br />
						<button type="button" class="btn btn-primary" id="preview" onclick="preview_button(1)">Preview</button>
						※必ずURLが有効であるか確認してください。
					</div>
					<div class="card-body" id="str_count" style="display:none;">
						文字数：<span id="strNum">0</span><span style="display:none;color:red;margin-left:10px" id="warning_str">{{ Config::get('const.sms_warning_msg') }}</span><br />
						<font color="red">※置き換え文字を抜いた文字数を表示しています。置き換え後の文字列を含め、文字数が71文字以上の場合追加料金が発生することがありますのでご注意ください。</font>
					</div>
				</div>
				<br /><br />
				@include('layouts.template_custome')
				<div class="row justify-content-center">
					<button type="button" onclick="clear_button(0)" class="btn btn-outline-secondary" style="margin-right:10px">内容をクリア</button>
					<button type="subimit" class="btn btn-outline-primary" id="template_regist">登録</button>
				</div>
				<div class="row justify-content-center">
					<span style="display:none;color:red;margin-left:10px" id="warning_str_seigen">SMSは660文字以上送ることはできません。</span>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
