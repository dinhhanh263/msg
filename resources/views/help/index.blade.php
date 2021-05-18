@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card" id="csv_about">
				<div class="card-header">CSVファイルについて</div>
				<div class="card-body">
					<p>
						・拡張子が「.csv」のファイルをCSVファイルといいます。（例：test.csv（「.」以降が拡張子です。）<br />
						・CSVファイルは【カンマ区切り】で列が指定されています。<br />

					</p>

				</div>
			</div>
		</div>
	</div>
</div>
@endsection
