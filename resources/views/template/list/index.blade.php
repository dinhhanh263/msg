@extends('layouts.app')

<script src="../js/template.js" defer></script>

@section('content')
<div class="container-fluid">
	<div class="row justify-content-center" style="padding-left:90px;padding-right:100px;padding-bottom:10px;">
		@if (!empty($message['SUCCESS']))
			<span class="list-group-item list-group-item-action list-group-item-success">{{ $message['SUCCESS'] }}</span>
			<br />
		@endif
	</div>
	<div class="d-flex justify-content-start" style="padding-left:90px;">
		<span style="font-size:2em">配信登録/テンプレート一覧</span>
		<button type="button" onclick="location.href='{{ route('templateCreate') }}?from=list'" class="btn btn-primary" style="margin-bottom:10px;margin-left:30px;">新規作成</button>
		<br />
	</div>
	<div class="row justify-content-center">
		<div class="col-lg-3">
			<div class="card">
				<div class="card-header">検索</div>
				<div class="card-body">
					<form action="{{ route('templateList') }}" method="post" enctype="multipart/form-data">
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
				<div class="card-header">検索結果</div>
				<div class="card-body">
					@if ($ary_mail_template->isEmpty())
						テンプレートが存在しません。
					@else
						<table class="table">
							<thead class="thead-light">
								<tr>
									<th style="width:50%">テンプレート名</th>
									<th>配信種別</th>
									<th>最終更新</th>
									<th>編集</th>
									<th>配信登録</th>
								</tr>
							</thead>
							<tbody>
							@foreach ($ary_mail_template as $ary_item)
								<tr>
									<td>{{ $ary_item['template_name'] }}</td>
									<td>{{ Config::get('const.send_type.' . $ary_item['send_type']) }}</td>
									<td>{{ $ary_item['updated_at'] }}</td>
									<td><button type="button" class="btn btn-success" onclick="location.href='{{ route('templateEdit', ['id' => $ary_item['id']]) }}'">編集</button></td>
									<td><button type="button" class="btn btn-primary" onclick="location.href='{{ route('sendId', ['id' => $ary_item['id']]) }}'">配信</button></td>
								</tr>
							@endforeach
							</tbody>
						</table>
						<div class="d-flex justify-content-center">
							{{ $ary_mail_template->appends($request)->links() }}
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
