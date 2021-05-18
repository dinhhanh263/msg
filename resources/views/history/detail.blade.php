@extends('layouts.app')

<script src="{{ asset('js/history.js') }}" defer></script>

@section('content')
<div class="container">
	<span style="font-size:2em">配信登録履歴詳細</span>
	<br />
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">配信登録結果</div>
				<div class="card-body">
					<table class="table">
						<tbody>
							<tr>
								<th style="width:100px;">日時</th>
								<td>{{ $ary_mail_send_result[0]['reg_date'] }}</td>
							</tr>
							<tr>
								<th>結果</th>
								<td>
									@if ($ary_mail_send_result[0]['regist_status'] == 0)
										成功
										@if (!empty($ary_mail_send_result[0]['regist_fails']))
											（フォーマットエラーあり）
											<form action="{{ route('historyGetCsv') }}" method="post" enctype="multipart/form-data">
												@csrf
												<input type="hidden" name="request_id" value="{{ $ary_mail_send_result[0]['request_id'] }}">
												※フォーマットエラーを確認する場合はCSVを出力して確認してください。<br />
												<button type="subimit" class="btn btn-danger">バリデーションエラーCSV</button>
											</form>
										@endif
									@else
										失敗
									@endif
								</td>
							</tr>
							@if ($ary_mail_send_result[0]['send_type'] == 1)
								<tr>
									<th>件名</th>
									<td>{{ $ary_mail_send_result[0]['title'] }}</td>
								</tr>
							@endif
							<tr>
								<th>本文</th>
								<td>
									<textarea style="height:300px;resize:none;" class="form-control" readonly>{{ $ary_mail_send_result[0]['template'] }}@if (!empty($ary_mail_send_result[0]['url'])) {{ $ary_mail_send_result[0]['url'] }} @endif</textarea>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		@if ($ary_mail_send_result[0]['send_type'] == Config::get('const.SEND_TYPE_SMS'))
			@include('layouts.sms_links_result')
		@endif
	</div>
</div>
@endsection
