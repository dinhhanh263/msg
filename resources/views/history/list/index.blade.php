@extends('layouts.app')

<script src="../js/template.js" defer></script>

@section('content')
<div class="container-fluid">
	<div class="d-flex justify-content-start" style="padding-left:90px;">
		<span style="font-size:2em">配信登録履歴一覧</span>
		<br />
	</div>
	<div class="row justify-content-center">
		<div class="col-lg-3">
			<div class="card">
				<div class="card-header">検索</div>
				<div class="card-body">
					<form action="{{ route('historyList') }}" method="post" enctype="multipart/form-data">
						@csrf
						<table class="table">
							<thead class="thead-light">
								<tr>
									<th>配信種別</th>
									<th>
										<select name="send_type" class="custom-select">
											<option value="">-</option>
											@foreach ($send_type as $send_type_key => $send_type_value)
												<option value="{{ $send_type_key }}" {{ old('send_type') == $send_type_key ? 'selected' : '' }}>{{ $send_type_value }}</option>
											@endforeach
										</select>
									</th>
								</tr>
								<tr>
									<td><b>配信元事業部</b></td>
									<td>
										<select name="type" class="custom-select">
											<option value="">-</option>
											@foreach ($type as $type_key => $type_value)
												<option value="{{ $type_key }}" {{ old('type') == $type_key ? 'selected' : '' }}>{{ $type_value }}</option>
											@endforeach
										</select>
									</td>
								</tr>
								<tr>
									<th>配信結果</th>
									<th>
										<select name="regist_status" class="custom-select">
											<option value="">-</option>
											@foreach ($regist_status as $regist_status_key => $regist_status_value)
												<option value="{{ $regist_status_key }}" {{ old('regist_status') == $regist_status_key ? 'selected' : '' }}>{{ $regist_status_value }}</option>
											@endforeach
										</select>
									</th>
								</tr>
							</thead>
						</table>
						<div class="row justify-content-center">
							<button type="submit" class="btn btn-primary">検索</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">検索結果（直近3ヶ月以内の送信結果を表示）</div>
				<div class="card-body">
					@if (empty($ary_mail_template->items()))
						直近3ヶ月以内の配信登録結果はありません。
					@else
						※配信登録結果は実際の送信結果とは異なります。
						<table class="table">
							<thead class="thead-light">
								<tr>
									<th>配信登録日時</th>
									<th>配信登録結果</th>
									<th>配信テンプレート名</th>
									<th>配信種別</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($ary_mail_template as $ary_item)
								<tr>
									<td><a href="{{ route('historyDetail', ['request_id' => $ary_item['request_id']]) }}">{{ $ary_item['reg_date'] }}</a></td>
									<td>
										@if ($ary_item['regist_status'] == 0)
											成功
										@else
											失敗
										@endif
									</td>
									<td>{{ $ary_item['template_name'] }}</td>
									<td>{{ Config::get('const.send_type.' . $ary_item['send_type']) }}</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					@endif
				</div>
				<div class="d-flex justify-content-center">
				{{ $ary_mail_template->appends($request)->links() }}
			</div>
			</div>
		</div>
	</div>
</div>
@endsection
