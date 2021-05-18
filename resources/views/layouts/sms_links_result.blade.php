<!-- 配信結果700 -->
@if ($ary_mail_send_result['total'] != 0)
	<div class="col-md-8" style="margin-top:10px">
		<div class="card">
			<div class="card-header">配信失敗一覧({{ $ary_mail_send_result['total'] }}件)</div>
			<div class="card-body">
				<table class="table table-sm">
					<thead>
						<tr>
							<th>電話番号</th>
							<th>理由</th>
						</tr>
					</thead>
					<tbody id="more_send_no">
						@foreach ($ary_mail_send_result['contacts'] as $value)
							@if ($loop->iteration <= 5)
								<tr id="more_send_no{{ $loop->iteration }}">
									<td>{{ $value['phone_number'] }}</td>
									<td>{{ $value['detail_message'] }}</td>
								</tr>
							@else
								<tr id="more_send_no{{ $loop->iteration }}" style="display:none">
									<td>{{ $value['phone_number'] }}</td>
									<td>{{ $value['detail_message'] }}</td>
								</tr>
							@endif
						@endforeach
					</tbody>
				</table>
				@if ($ary_mail_send_result['total'] >= 6)
					<a href="javascript:send_no_more('no');" id="more_send_no_button" class="btn btn-primary btn-lg btn-block">もっと見る</a>
				@endif
			</div>
		</div>
	</div>
@endif