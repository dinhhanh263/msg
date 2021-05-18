<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>配信登録システム</title>

	<!-- Scripts -->
	<script src="{{ asset('js/app.js') }}" defer></script>
	<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

	<!-- Fonts -->
	<link rel="dns-prefetch" href="//fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

	<!-- Styles -->
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('css/common.css') }}" rel="stylesheet">
</head>
<body>
	<div id="app">
		<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
			<div class="container-fluid">
				<a class="navbar-brand" href="{{ url('/top') }}">
					<img class="logo" src="{{ asset('img/menu_vielis.png') }}" alt="logo">
				</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<!-- Left Side Of Navbar -->
					<!-- ログイン済みの場合のみ表示 -->
					@if (!empty(session('users')))

						<ul class="navbar-nav mr-auto">
							<!-- ログイン済みの場合飲み表示 -->
							<li class="nav-item" style="margin:10px"><a href="/template">配信登録/テンプレート一覧</a></li>
							<li class="nav-item" style="margin:10px"><a href="/history">配信登録履歴</a></li>
							<li class="nav-item" style="margin:10px"><a href="/upload">アップロード</a></li>
							<!-- <li class="nav-item" style="margin:10px"><a href="/help">ヘルプ</a></li> -->
						</ul>

						<ul class="navbar-nav ml-auto">
							<!-- ログイン済みの場合飲み表示 -->
							<li class="nav-item" style="margin:10px">名前：{{ session('users.login_id') }}</li>
							<li class="nav-item" style="margin:10px"><a href="/logout">ログアウト</a></li>
						</ul>
					@endif
				</div>
			</div>
		</nav>

		<main class="py-4">
			@yield('content')
		</main>
	</div>
</body>
</html>