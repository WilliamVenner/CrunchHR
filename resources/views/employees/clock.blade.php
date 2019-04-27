@extends('layout')

@section('title', 'Attendance')

@section('css-includes')
	<link rel="stylesheet" href="/assets/css/attendance.css" type="text/css" />
@endsection

@section('center-content')
	<div id="clock-date">{{ date('l jS F Y') }}</div>
	<div id="clock-time">{{ date('h:i:sa') }}</div>
	
	<div class="spacer px20"></div>
	
	<i class="fas fa-clock" style="font-size:100px"></i>
	
	<div class="spacer px20"></div>
	
	@if ($TodaysAttendance === null)
		You have not clocked in today!
	@else
		@if ($TodaysAttendance -> clocked_in !== null)
			<i class="fas fa-sign-in-alt"></i> You clocked IN today @component('partials.timestamp') @slot('timestamp', strtotime($TodaysAttendance -> clocked_in)) @endcomponent
		@endif
		@if ($TodaysAttendance -> clocked_out !== null)
			<div class="spacer px10"></div>
			<i class="fas fa-sign-out-alt"></i> You clocked OUT today @component('partials.timestamp') @slot('timestamp', strtotime($TodaysAttendance -> clocked_out)) @endcomponent
		@endif
	@endif
	
	<div class="spacer px20"></div>
	
	<form method="POST" id="attendance-form">
		@csrf
		
		<input type="hidden" name="clock-in" id="clock-in"/>
		<input type="hidden" name="clock-out" id="clock-out"/>
		
		@component('partials.button')
			@slot('color', 'green')
			@slot('text', 'Clock IN')
			@slot('icon', 'fas fa-sign-in-alt')
			@slot('disabled', $TodaysAttendance !== null && $TodaysAttendance -> clocked_in !== null)
			@slot('id', 'clock-in-btn')
		@endcomponent
		
		@component('partials.button')
			@slot('color', 'red')
			@slot('text', 'Clock OUT')
			@slot('icon', 'fas fa-sign-out-alt')
			@slot('disabled', $TodaysAttendance === null || $TodaysAttendance -> clocked_out !== null)
			@slot('id', 'clock-out-btn')
		@endcomponent
		
		<div class="spacer px10"></div>
		
		<a href="/employees/me/leave">
			@component('partials.button')
				@slot('color', 'orange')
				@slot('text', 'Request Absence')
				@slot('icon', 'fas fa-user-slash')
			@endcomponent
		</a>
	</form>
@endsection

@section('js-includes')
	<script type="text/javascript" src="/assets/js/attendance.js"></script>
@endsection