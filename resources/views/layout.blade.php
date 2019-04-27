<html>
	<head>
		<meta charset="utf-8">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		
		@hasSection('title')
			<title>CrunchHR &middot; @yield('title')</title>
		@else
			@hasSection('content-box-title')
				<title>CrunchHR &middot; @yield('content-box-title')</title>
			@else
				<title>CrunchHR</title>
			@endif
		@endif
		
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
		<link rel="stylesheet" href="/assets/css/app.css" type="text/css" />
		@yield('css-includes')
	</head>
	<body>
		<div id="sidebar"><div>
			<a href="/"><img class="logo" src="/assets/img/crunchhr_dark.png"/></a>
			
			<a href="/employees/me"><div class="profile">
				<img class="avatar" src="{{ $UserEmployee -> picture('jpg') }}"/>
				<span class="name">{{ $UserEmployee -> full_name() }}</span>
			</div></a>
			
			<a href="/announcements"><div class="sidebar-item">
				<span><i class="fas fa-bullhorn"></i></span>
				<span>Announcements</span>
			</div></a>
			
			<?php $notification_count = \App\Models\Notification::where(['seen' => '0', 'employee_id' => $UserEmployee -> id])->count(); ?>
			<a href="/notifications"><div class="sidebar-item" {!! $notification_count > 0 ? 'style="font-weight:bold"' : '' !!}>
				<span><i class="fas fa-bell"></i></span>
				<span>Notifications ({{ $notification_count }})</span>
			</div></a>
			
			<div class="sidebar-category">
				Employee
			</div>
			
			<a href="/employees/me/attendance"><div class="sidebar-item">
				<span><i class="fas fa-business-time"></i></span>
				<span>Clock In/Out</span>
			</div></a>
			
			<a href="/employees/me"><div class="sidebar-item">
				<span><i class="fas fa-user-circle"></i></span>
				<span>My Profile</span>
			</div></a>
			
			<a href="/employees/me/leave"><div class="sidebar-item">
				<span><i class="fas fa-calendar-check"></i></span>
				<span>My Leave</span>
			</div></a>
			
			<a href="/employees/me/1to1"><div class="sidebar-item">
				<span><i class="fas fa-comments"></i></span>
				<span>My 1 to 1s</span>
			</div></a>
			
			<a href="/employees/me/keycard"><div class="sidebar-item">
				<span><i class="fas fa-id-card"></i></span>
				<span>My Keycard</span>
			</div></a>
			
			<a href="/employees/me/files"><div class="sidebar-item">
				<span><i class="fas fa-file"></i></span>
				<span>My Files</span>
			</div></a>
			
			<div class="sidebar-item" id="signout">
				<span><i class="fas fa-sign-out-alt"></i></span>
				<span>Sign out</span>
			</div>
			
			@if ($UserEmployee -> is_human_resources())
				<div class="sidebar-category">
					Human Resources
				</div>
				
				<a href="/employees"><div class="sidebar-item">
					<span><i class="fas fa-user-tie"></i></span>
					<span>Employees</span>
				</div></a>
				
				<a href="/hr/recruitment"><div class="sidebar-item">
					<span><i class="fas fa-user-plus"></i></span>
					<span>Recruitment</span>
				</div></a>
				
				<a href="/hr/1to1"><div class="sidebar-item">
					<span><i class="fas fa-comments"></i></span>
					<span>1 to 1s</span>
				</div></a>
				
				<a href="/hr/attendance"><div class="sidebar-item">
					<span><i class="fas fa-business-time"></i></span>
					<span>Attendance</span>
				</div></a>
				
				<a href="/hr/leave"><div class="sidebar-item">
					<span><i class="fas fa-calendar-check"></i></span>
					<span>Leave Requests</span>
				</div></a>
			@endif
			
			@if ($UserEmployee -> is_management())
				<div class="sidebar-category">
					Management
				</div>
				
				<a href="/announcements/new"><div class="sidebar-item">
					<span><i class="fas fa-bullhorn"></i></span>
					<span>Announcement</span>
				</div></a>
				
				<a href="/management/departments"><div class="sidebar-item">
					<span><i class="fas fa-users"></i></span>
					<span>Departments</span>
				</div></a>
			@endif
			
			<div class="copyright">
				&copy; <?=date('Y');?> CrunchHR
			</div>
		</div></div>
		
		@hasSection('content-header')
			<div id="page-header">@yield('content-header')</div>
		@endif
		<div id="content-container">
			@hasSection('center-content')
				<div id="content" style="text-align:center">
					@yield('center-content')
				</div>
			@else
				<div id="content">
					@hasSection('content')
						@yield('content')
					@else
						@hasSection('content-box')
							<div id="content-box">
								@hasSection('content-box-title')
									<div class="title">
										<div class="icon"><i class="@yield('content-box-icon')"></i></div>
										<span>@yield('content-box-title')</span>
										@hasSection('content-box-back')
											<a href="@yield('content-box-back')"><i class="fas fa-backward"></i> BACK</a>
										@endif
									</div>
								@endif
								<div class="content">
									@yield('content-box')
								</div>
							</div>
						@endif
					@endif
				</div>
			@endif
		</div>
		
		<div id="employee-selector" class="hidden loading">
			<div class="loading">
				<img src="/assets/img/loading.svg"/>
				<span>Loading...</span>
			</div>
			<div class="error">Failed to load</div>
			<div class="no-results">No results found</div>
		</div>
		
		<div id="loading-overlay" class="hidden"></div>
		
		<div id="modals">
			@yield('modals')
		</div>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://unpkg.com/tippy.js@3/dist/tippy.all.min.js"></script>
		<script type="text/javascript" src="/assets/js/lib/moment.min.js"></script>
		<script type="text/javascript" src="/assets/js/app.js"></script>
		<script type="text/javascript">
			/* global CrunchHR */
			CrunchHR.Notifications.PublicKey = "{{ config('notifications.NOTIFICATION_PUBLIC_KEY') }}";
			CrunchHR.UserEmployee = {{ $UserEmployee -> id }};
		</script>
		@yield('js-includes')
	</body>
</html>