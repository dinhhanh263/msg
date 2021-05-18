@extends('layouts.app')

<script src="{{ asset('js/send.js') }}" defer></script>

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">配信登録確認画面</div>
				<div class="card-body">
					登録可能件数：{{ $int_success_cnt }}件<br />
					フォーマットチェックエラー件数：{{ $int_error_cnt }}件<br />
					@if (!empty($ary_valid_error))
						<table class="table">
							<thead class="thead-light">
								<tr>
									<th>対象行</th>
									<th>エラー箇所</th>
									<th>エラー内容</th>
								</tr>
							</thead>
							@foreach ($ary_valid_error as $key => $value)
								@if ($loop->iteration > 3 && $int_error_cnt > 5)
									@break
								@else
									<tr>
										<td>{{ $value['num'] }}</td>
										<td>{{ $value['failCustomerKey'] }}</td>
										<td>{{ $value['faliReason'] }}</td>
									</tr>
								@endif
							@endforeach
							@if ($int_error_cnt > 5)
									<tr>
										<td></td>
										<td>
											・<br />
											・<br />
											・<br />
										</td>
										<td></td>
									</tr>
									<tr>
										<td>{{ $ary_valid_error[$int_error_cnt -1]['num'] }}</td>
										<td>{{ $ary_valid_error[$int_error_cnt -1]['failCustomerKey'] }}</td>
										<td>{{ $ary_valid_error[$int_error_cnt -1]['faliReason'] }}</td>
									</tr>
							@endif
						</table>
						@if ($int_error_cnt > 5)
							<form action="{{ route('getCsv') }}" method="post" enctype="multipart/form-data">
								@csrf
								<div class="d-flex justify-content-center"><button type="subimit" class="btn btn-danger">フォーマットチェック結果をダウンロード</button></div>
								※6件以上のエラーを確認する場合はcsvをダウンロードしてエラー内容を確認してください。
							</form>
						@endif
					@endif
				</div>
			</div>
			<br /><br />
			<form action="{{ route('sendApiConfirm') }}" method="post" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="select_template" value="{{ $ary_confirm_data['select_template'] }}" />
				<input type="hidden" name="send_required" value="{{ $ary_confirm_data['send_required'] }}" />
				<input type="hidden" name="head_line" value="{{ $ary_confirm_data['head_line'] }}" />
				<input type="hidden" name="type" value="{{ $ary_confirm_data['type'] }}" />
				<input type="hidden" name="send_type" value="{{ $ary_confirm_data['send_type'] }}" />
				<input type="hidden" name="import_flg" value="{{ $ary_confirm_data['import_flg'] }}" />
				<input type="hidden" name="import_method" value="{{ $ary_confirm_data['import_method'] }}" />
				<input type="hidden" name="customer_no" value="{{ $ary_confirm_data['customer_no'] }}" />
				<div class="card">
					<div class="card-header">配信登録内容</div>
					@if ($ary_confirm_data['send_type'] == 1)
						<div class="card-body">
						 件名<br />
						<input type="text" name="title" class="form-control" id="title" value="{{ $ary_confirm_data['title'] }}" readonly />
						</div>
					@endif
					<div class="card-body">
						本文<br />
						<textarea name="text" id="text" style="height:300px;resize:none;" class="form-control" readonly>{{ $ary_confirm_data['text'] }}</textarea>
						@if ($ary_confirm_data['send_type'] == Config::get('const.SEND_TYPE_SMS'))
							<br />
							文字数：<span id="strNum">{{ $ary_confirm_data['str_num'] }}</span>
							@if ($ary_confirm_data['str_num'] > 70)
								<span style="color:red;margin-left:10px" id="warning_str">{{ Config::get('const.sms_warning_msg') }}</span>
							@endif
							<br />
							<font color="red">※置き換え文字を抜いた文字数を表示しています。置き換え後の文字列を含め、文字数が70文字以上の場合追加料金が発生することがありますのでご注意ください。</font>
						@endif
					</div>
				</div>
				<br><br>
				<br />
				<div class="d-flex justify-content-center">
					<button type="submit" name="action" value="back" onclick="clear_button(0)" class="btn btn-outline-secondary" style="margin-right:10px">戻る</button>
					<button type="subimit" name="action" value="submit" class="btn btn-outline-primary" {{ $int_success_cnt > 0 ? '' : 'disabled' }}>配信登録</button>
				</div>
				@if ($int_success_cnt <= 0)
					<div class="d-flex justify-content-center">
						<span style="color:red;">※登録可能件数が0件のため、配信登録ができません。</span>
					</div>
				@endif
			</form>
		</div>
	</div>
</div>
@endsection