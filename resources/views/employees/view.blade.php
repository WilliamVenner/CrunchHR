<?php if (!isset($Employee)) $Employee = $UserEmployee; ?>

@extends('layout')

@section('css-includes')
	<link rel="stylesheet" href="/assets/css/employees.view.css" type="text/css" />
@endsection

@section('content-box')
	<div class="h-center" id="employee-header">
		<img src="{{ $Employee -> picture('png') }}" class="avatar"/>
		<div class="name">{{ $Employee -> full_name() }}</div>
		@if ($Employee -> job)
			<div class="job">{{ $Employee -> job -> title }}</div>
			<div class="department">{{ $Employee -> job -> department -> name }}</div>
		@endif
		<div class="email">
			<a href="mailto:{{ $Employee -> email }}">{{ $Employee -> email }}</a>
		</div>
		@if ($Employee -> signup_code !== null && $UserEmployee -> is_human_resources())
			<div class="signupcode">
				Signup code
				<div>{{ $Employee -> signup_code }}</div>
			</div>
		@endif
	</div>
	<div class="tabs">
		<a href="/employees/{{ $EmployeeURLID }}"><div class="tab {{ $_SERVER['REQUEST_URI'] === '/employees/' . $EmployeeURLID ? 'active' : '' }}" style="border-color:#007eff">Details</div></a>
		@if ($UserEmployee -> is_human_resources() || $UserEmployee -> id == $Employee -> id)
			@if ($Employee -> id !== $UserEmployee -> id)
				<a href="/employees/{{ $EmployeeURLID }}/notations"><div class="tab {{ $_SERVER['REQUEST_URI'] === '/employees/' . $EmployeeURLID . '/notations' ? 'active' : '' }}" style="border-color:#ff8100">Notations</div></a>
			@endif
			<a href="/employees/{{ $EmployeeURLID }}/leave"><div class="tab {{ substr($_SERVER['REQUEST_URI'], 0, strlen('/employees/' . $EmployeeURLID . '/leave')) === '/employees/' . $EmployeeURLID . '/leave' ? 'active' : '' }}" style="border-color:#3fb900">Leaves</div></a>
			<a href="/employees/{{ $EmployeeURLID }}/1to1"><div class="tab {{ $_SERVER['REQUEST_URI'] === '/employees/' . $EmployeeURLID . '/1to1' ? 'active' : '' }}" style="border-color:#c0f">1 to 1s</div></a>
			@if ($UserEmployee -> is_management() || $UserEmployee -> id == $Employee -> id)
				<a href="/employees/{{ $EmployeeURLID }}/keycard"><div class="tab {{ $_SERVER['REQUEST_URI'] === '/employees/' . $EmployeeURLID . '/keycard' ? 'active' : '' }}" style="border-color:#f90000">Keycard</div></a>
				<a href="/employees/{{ $EmployeeURLID }}/files"><div class="tab {{ $_SERVER['REQUEST_URI'] === '/employees/' . $EmployeeURLID . '/files' ? 'active' : '' }}" style="border-color:#ffc800">Files</div></a>
			@endif
		@endif
	</div>
	@hasSection("employee-content-center")
		<div id="employee-content" style="text-align:center">
			@yield('employee-content-center')
		</div>
	@else
		<div id="employee-content">
			@yield('employee-content')
		</div>
	@endif
@endsection

@section('js-includes')
	<script type="text/javascript" src="/assets/js/employee.view.js"></script>
	<script type="text/javascript">
		const ViewingEmployee = {{ $Employee -> id }};
	</script>
@endsection