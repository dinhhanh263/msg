@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">最新の配信登録履歴（過去3ヶ月以内 かつ 最大5件まで表示。）</div>
				<div class="card-body">
					@if (empty($ary_latest_result))
						配信登録履歴はありません。
					@else
						<table class="table">
							<thead class="thead-light">
								<tr>
									<th>配信登録日</th>
									<th>配信結果</th>
									<th>テンプレート名</th>
									<th>配信種別</th>
								</tr>
							</thead>
							<tbody>
								@foreach ($ary_latest_result as $ary_item)
									<tr>
										<td><a href="{{ route('historyDetail', ['request_id'=> $ary_item['request_id']]) }}">{{ $ary_item['reg_date'] }}</a></td>
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
						<div class="d-flex justify-content-center"><button type="button" class="btn btn-primary" onclick="location.href='{{ route('historyList') }}'">さらに見る</button></div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
