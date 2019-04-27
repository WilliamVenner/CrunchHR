<?php
	$month = $month !== null ? $month : intval(date('n'));
	$year = $year !== null ? $year : intval(date('Y'));
	$first_day_weekday = date('w', strtotime("1-$month-$year"));
	
	$department_colors_generated = \Colors\RandomColor::many($department_count, ['luminosity' => 'dark']);
	$department_colors_index = 0;
	$department_colors = [];
	function GetDepartmentColor($department_id, &$department_colors_generated, &$department_colors_index, &$department_colors) {
		if (!isset($department_colors[$department_id])) {
			$department_colors[$department_id] = $department_colors_generated[$department_colors_index];
			$department_colors_index += 1;
		}
		return $department_colors[$department_id];
	}
?>

@extends('layout')

@section('title', 'Attendance')

@section('css-includes')
	<link rel="stylesheet" href="/assets/css/attendance.css" type="text/css" />
@endsection

@section('center-content')
	<form method="POST">
		@csrf
		<input type="month" value="{{ $year }}-{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}" name="year-month"/>
		<div class="spacer px5"></div>
		@component('partials.button')
			@slot('color', 'green')
			@slot('text', 'Submit')
			@slot('icon', 'fas fa-sign-out-alt')
			@slot('small', true)
			@slot('submit', true)
		@endcomponent
	</form>
	
	<div class="spacer px20"></div>
	
	<table class="calendar">
		<thead>
			<tr>
				<th>Sun</th>
				<th>Mon</th>
				<th>Tue</th>
				<th>Wed</th>
				<th>Thu</th>
				<th>Fri</th>
				<th>Sat</th>
			</tr>
		</thead>
		<tbody>
			<tr>
			@for ($i = 1; $i <= intval(date('t')); $i++)
				@if ($i === 1 && $first_day_weekday > 0)
					<td class="buffer" colspan="{{ $first_day_weekday }}"></td>
				@endif
				<td data-day="{{ $i }}">
					@if (isset($department_attendance[$i]))
						<span class="departments" style="grid-template-columns: {{ str_repeat('1fr ', $department_count) }}">
							@foreach($department_attendance[$i] as $k => $v)
								<span class="department" style="height:{{ $v -> attendance_pct }}%;background-color:{{ GetDepartmentColor($v -> department_id, $department_colors_generated, $department_colors_index, $department_colors) }}"></span>
							@endforeach
						</span>
					@endif
					<span class="text">{{ \App\Helpers::NumberWithOrdinal($i) }}</span>
				</td>
				
				@if (($i + $first_day_weekday) % 7 === 0)
					</tr><tr>
				@endif
			@endfor
		</tbody>
	</table>
	
	<div class="spacer px20"></div>
	<table class="styled" id="attendance-details">
		<thead>
			<tr>
				<th>Department</th>
				<th>Employees</th>
				<th>Attendance</th>
			</tr>
		</thead>
		<tbody>
			<tr class="tip">
				<td colspan="3">Click a day on the calendar to see the attendance of each department</td>
			</tr>
		</tbody>
	</table>
@endsection

@section('js-includes')
	<script type="text/javascript" src="/assets/js/attendance.js"></script>
	
	<?php
		$department_data = collect(DB::select('SELECT `id`, `name`, (SELECT COUNT(*) FROM employees WHERE `job_id` IN (SELECT `id` FROM department_jobs WHERE `department_id`=department.`id`)) AS `employee_count` FROM department'))->keyBy('id');
	?>
	<script type="text/javascript">
		var ATTENDANCE_DATA = {!! json_encode($department_attendance) !!};
		var DEPARTMENTS = {!! json_encode($department_data) !!};
	</script>
@endsection