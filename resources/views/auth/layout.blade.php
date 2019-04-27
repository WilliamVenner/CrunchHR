<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		
		<title>CrunchHR &middot; @yield('title')</title>
		
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
		<link rel="stylesheet" href="/assets/css/app.css" type="text/css" />
		<link rel="stylesheet" href="/assets/css/auth.css" type="text/css" />
		
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	</head>
	<body>
		<div id="auth-container">
			<img src="/assets/img/crunchhr_dark.png" id="logo"/><br>
			
			<div id="auth-box">
				@yield('content')
			</div>
		</div>
		
		@yield('post-content')
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://unpkg.com/tippy.js@3/dist/tippy.all.min.js"></script>
		<script type="text/javascript" src="/assets/js/lib/moment.min.js"></script>
		<script type="text/javascript" src="/assets/js/auth.js"></script>
	</body>
</html>