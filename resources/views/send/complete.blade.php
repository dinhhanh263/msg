@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			配信登録が完了しました。詳細は<button type="button" class="btn btn-primary btn-sm" onclick="location.href='{{ route('historyDetail', ['request_id' => $request_id]) }}'">こちら</button>から確認してください。
		</div>
	</div>
</div>
@endsection
