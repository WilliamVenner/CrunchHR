@extends('employees.view')

@section('employee-content-center')
	<div class="tabs">
		@if ($EmployeeURLID === 'me')
			<a href="/employees/me/leave">
				<div class="tab {{ Request::route()->getName() === 'leave.request' ? 'active' : '' }}" style="border-color:#ff0000">Request</div>
			</a>
		@endif
		<a href="/employees/{{ $EmployeeURLID }}/leave/history">
			<div class="tab {{ Request::route()->getName() === 'leave.history' ? 'active' : '' }}" style="border-color:#00a1ff">Leave History</div>
		</a>
		<a href="/employees/{{ $EmployeeURLID }}/leave/calendar">
			<div class="tab {{ Request::route()->getName() === 'leave.calendar' ? 'active' : '' }}" style="border-color:#00cc00">Calendar</div>
		</a>
	</div>
	@hasSection('leave-content-center')
		<div class="tab-content h-center">
			@yield('leave-content-center')
		</div>
	@else
		<div class="tab-content">
			@yield('leave-content')
		</div>
	@endif
@endsection