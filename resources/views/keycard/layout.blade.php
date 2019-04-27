<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<title>CrunchHR &middot; Keycard Login</title>
		
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
		<link rel="stylesheet" href="/assets/css/app.css" type="text/css" />
		<style type="text/css">
		
			#keycard-content {
				position: absolute;
				width: 100%;
				height: 100%;
				text-align: center;
				display: flex;
				flex-direction: column;
				left: 0;
				align-items: center;
				justify-content: center;
			}
			#keycard-content > div {
				width: 477px;
				background-color: #fff;
				border: 1px solid #d0d0d0;
				box-shadow: 0 0 5px rgba(0, 0, 0, 0.25);
				padding: 20px;
			}
			
			#expired, #success {
				font-size: 64px;
			}
			#expired {color: #d00000}
			#success {color: #008000}
			
			@media (max-width: 517px) {
				#keycard-content > div {
					width: auto;
					box-shadow: none;
					background-color: transparent;
					border: none;
				}
			}
		</style>
	</head>
	<body>
		<div id="keycard-content"><div>
			@yield('content')
		</div></div>
	</body>
</html>