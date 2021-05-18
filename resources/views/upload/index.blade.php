@extends('layouts.app')

<script src="{{ asset('js/upload.js') }}" defer></script>

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			@if (!empty($success_msg))
				<span class="list-group-item list-group-item-action list-group-item-success" style="margin-bottom:10px">{{ $success_msg }}</span>
				<br />
			@elseif (!empty($error_msg))
				<span class="list-group-item list-group-item-action list-group-item-danger" style="margin-bottom:10px">{{ $error_msg }}</span>
				<br />
			@endif
			<div class="card">
				<div class="card-header">ファイルアップロード</div>
				<div class="card-body">
					<form method="POST" action="{{ route('uploadPost') }}" enctype="multipart/form-data">
						@csrf
						PDFのファイルを指定してください。
						<input type="file" value="ファイルを選択" name="pdf_file" accept=".pdf" class="form-control-file @error('pdf_file') is-invalid @enderror" value="{{ old('pdf_file') }}">
						@error('pdf_file')
							<span class="invalid-feedback" role="alert">
								<strong>{{ $message }}</strong>
							</span>
						@enderror
						<br />
						<button type="subimit" class="btn btn-outline-primary">アップロード</button>
					</form>
				</div>
			</div>
			<br />
			<div class="card">
				<div class="card-header">アップロードファイル一覧（最新10件）</div>
				<div class="card-body">
					@if (empty($ary_file_list))
						アップロードされたファイルはありません。
					@else
						<table class="table">
							<thead class="thead-light">
								<tr>
									<th class="tooltip1">
										アップロード名
										<div class="description1">アップロードしたときのファイル名</div>
									</th>
									<th>プレビュー</th>
									<th>テンプレート作成</th>
									<th class="tooltip1">
										URLコピー
										<div class="description1">クリップボードにURLをコピーします</div>
									</th>
								</tr>
								@foreach ($ary_file_list as $value)
									<tr>
										<td>{{ $value['label'] }}</td>
										<td><button type="button" class="btn btn-primary" onclick="window.open('{{ Config::get('azure.azure_base_url') . $value['file_name'] }}', '_blank')">Preview</button></td>
										<td><button type="button" class="btn btn-success" onclick="location.href='{{ route('templateCreate') }}?label_id={{ $value['id'] }}'">新規作成</button></td>
										<td><button type="button" class="btn btn-warning" onclick="copy_button('{{ Config::get('azure.azure_base_url') . $value['file_name'] }}')">Copy</button></td>
									</tr>
								@endforeach
							</thead>
						</table>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
