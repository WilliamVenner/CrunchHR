@extends('layout')

@section('title', 'Employee Directory')

@section('center-content')
	<?php
		$pages = ceil(\App\Models\Employee::count() / 40);
		$sorting = [['id', 'ASC'], ['first_name', 'ASC'], ['middle_name', 'ASC'], ['last_name', 'ASC'], ['department_id', 'ASC'], ['job_id', 'ASC']];
		
		$Employees = \App\Models\Employee::skip((\App\Pagination::Page($pages) - 1) * 40)->take(40)->get();
	?>
	<form method="POST" id="employees-tbl-form">
		@csrf
		
		@component('partials.pagination')
			@slot('pages', $pages)
		@endcomponent
		
		<div class="spacer px15"></div>
		
		<label for="search">
			Search:
			<input type="text" class="employee-selector" id="employee-search"/>
		</label>
		
		<div class="spacer px15"></div>
		
		<table class="styled">
			<thead>
				<tr>
					<th>#</th>
					<th>Picture</th>
					<th>First Name</th>
					<th>Middle Name(s)</th>
					<th>Last Name</th>
					<th>Department</th>
					<th>Job</th>
				</tr>
			</thead>
			<tbody>
				@foreach($Employees as $Employee)
					<tr>
						<td style="text-align:center"><a href="/employees/{{ $Employee -> id }}">{{ $Employee -> id }}</a></td>
						<td style="text-align:center"><img class="profile-picture" src="{{ $Employee -> picture() }}"/></td>
						<td>{{ $Employee -> first_name }}</td>
						<td>{{ $Employee -> middle_name }}</td>
						<td>{{ $Employee -> last_name }}</td>
						<td>
							@if (isset($Employee -> job))
								<a href="/management/departments/edit/{{ $Employee -> job -> department -> id }}">{{ $Employee -> job -> department -> name }}</a>
							@endif
						</td>
						<td>{{ isset($Employee -> job) ? $Employee -> job -> title : '' }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</form>
@endsection

@section('js-includes')
	<script type="text/javascript">
		/* global $ */
		$("#employee-search").on("employee-selected", function() {
			window.location = "/employees/" + $(this).data("employee-id");
		});
	</script>
@endsection