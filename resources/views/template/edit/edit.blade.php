@extends('layouts.app')

<script src="{{ asset('js/template.js') }}" defer></script>

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
			<form action="{{ route('templateEditConfirm', ['id' => $ary_edit_template[0]['id']]) }}" method="post" enctype="multipart/form-data" name="updateForm">
				@csrf
				<div class="card">
					<div class="card-header">テンプレート作成/編集</div>
					<div class="card-body">
						テンプレート名<br />
						<input type="text" name="template_name" class="form-control @error('template_name') is-invalid @enderror" value="{{ empty(old('template_name')) ? $ary_edit_template[0]['template_name'] : old('template_name') }}" required>
						@error('template_name')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
					</div>
					@if ($ary_edit_template[0]['send_type'] == 1)
						<div class="card-body">
							件名<br />
							<input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ empty(old('title')) ? $ary_edit_template[0]['title'] : old('title') }}">
							@error('title')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
						</div>
					@endif
					<div class="card-body">
						本文　{{ $ary_edit_template[0]['send_type'] == Config::get('const.SEND_TYPE_SMS') ? '※SMSを選択した場合、改行は無くなります。' : '' }}<br />
						<textarea name="text" id="text" style="height:300px;resize:none;" class="form-control @error('text') is-invalid @enderror">{{ empty(old('text')) ? $ary_edit_template[0]['text'] : old('text') }}</textarea>
						@error('text')
								<span class="invalid-feedback" role="alert">
									<strong>{{ $message }}</strong>
								</span>
							@enderror
					</div>
					@if ($ary_edit_template[0]['send_type'] == Config::get('const.SEND_TYPE_SMS'))
						<div class="card-body" id="url_div" style="display:none;">
							<input type="hidden" name="url" value="" id="url" />
							URL　※URLは一律23文字としてカウントします。URLが有効であるか確認してから入力してください。<br />
							<input type="radio" name="url_type" class="custom-radio" value="1" {{ !empty(old('url_type')) && old('url_type') == 1 || empty(old('url_type')) ? 'checked' : '' }}>　アップロードしたファイルを選択する　
							<input type="radio" name="url_type" class="custom-radio" value="2" {{ !empty(old('url_type')) && old('url_type') == 2 || empty($ary_edit_template[0]['upload_file_list_id']) && !empty($ary_edit_template[0]['url']) ? 'checked' : '' }}>　URLを入力する

							<span id="url_input_block">
								<input type="text" id="url_input" name="url_input" class="form-control @error('url_input') is-invalid @enderror" autocomplete="off" value="{{ !empty(old('url_input')) ? old('url_input') : empty($ary_edit_template[0]['upload_file_list_id']) ? $ary_edit_template[0]['url'] : 'https://kireimo.jp/' }}" />
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
										<option value="{{ $value['id'] }}" {{ !empty(old('url_choice')) && old('url_choice') == $value['id'] ? 'selected' : !empty($ary_edit_template[0]['upload_file_list_id']) && $ary_edit_template[0]['upload_file_list_id'] == $value['id'] ? 'selected' : '' }}>{{ $value['label'] }}</option>
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
							<button type="button" class="btn btn-primary" id="preview" onclick="preview_button(2)">Preview</button>
							※必ずURLが有効であるか確認してください。
						</div>
						<div class="card-body" id="str_count" style="display:none;">
							文字数：<span id="strNum">0</span><span style="display:none;color:red;margin-left:10px" id="warning_str">{{ Config::get('const.sms_warning_msg') }}</span><br />
							<font color="red">※置き換え文字を抜いた文字数を表示しています。置き換え後の文字列を含め、文字数が71文字以上の場合追加料金が発生することがありますのでご注意ください。</font>
						</div>
					@endif
				</div>
				<br /><br />
				<div class="card">
					<div class="card-header">配信登録設定</div>
					<div class="card-body">
						配信元事業部 <font color="red">※編集不可</font>
						<select name="type" id="type" class="custom-select" disabled>
							<option value="">-</option>
							@foreach ($type as $type_key => $type_value)
								<option value="{{ $type_key }}" {{ $ary_edit_template[0]['type'] == $type_key ? 'selected' : '' }}>{{ $type_value }}</option>
							@endforeach
						</select>
						<input type="hidden" name="type" value="{{ $ary_edit_template[0]['type'] }}" />
					</div>
					<div class="card-body">
						配信種別 <font color="red">※編集不可</font>
						<select name="send_type" class="custom-select" required disabled>
							<option value="">-</option>
							@foreach ($send_type as $send_type_key => $send_type_value)
								<option value="{{ $send_type_key }}" {{ $ary_edit_template[0]['send_type'] == $send_type_key ? 'selected' : '' }}>{{ $send_type_value }}</option>
							@endforeach
						</select>
						<input type="hidden" id="send_type" name="send_type" value="{{ $ary_edit_template[0]['send_type'] }}" />
					</div>
				</div>
				<br /><br />
				@include('layouts.template_custome')
				<div class="d-flex justify-content-center">
					<button type="button" class="btn btn-outline-secondary" style="margin-right:10px" id="clear_button">内容をクリア</button>
					<button type="subimit" class="btn btn-outline-primary" id="template_update">更新</button>
				</div>
				<div class="row justify-content-center">
					<span style="display:none;color:red;margin-left:10px" id="warning_str_seigen">SMSは660文字以上送ることはできません。</span>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
