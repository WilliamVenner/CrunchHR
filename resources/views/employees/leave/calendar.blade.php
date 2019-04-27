<?php
	$month = $month !== null ? $month : intval(date('n'));
	$year = $year !== null ? $year : intval(date('Y'));
	$first_day_weekday = date('w', strtotime("1-$month-$year"));
?>
@extends('employees.leave.layout')

@section('leave-content-center')
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
				<td data-day="{{ $i }}" style="{{ $absent_days[$i] === true ? 'background-color:rgba(255, 0, 0, 0.4)' : '' }}">
					<span class="text">{{ \App\Helpers::NumberWithOrdinal($i) }}</span>
				</td>
				
				@if (($i + $first_day_weekday) % 7 === 0)
					</tr><tr>
				@endif
			@endfor
		</tbody>
	</table>
@endsection

@section('js-includes')
	<script type="text/javascript" src="/assets/js/leave.js"></script>
@endsection