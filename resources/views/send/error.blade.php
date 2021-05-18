@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			@if (!empty($result_list))
				配信登録に失敗しました。CSVの{{ $result_list + ($head_line_flg ? 1 : 2) }}行目以降送信できませんでした。<br />
				お手数ですが情報システム部までお問い合わせください。
			@else
				配信登録に失敗しました。お手数ですが情報システム部までお問い合わせください。
			@endif
		</div>
	</div>
</div>
@endsection
