@extends('layout')

@section('content-box-title', 'Departments')
@section('content-box-icon', 'fas fa-users')
@section('content-box')
	<a href="/management/departments/new"><div class="btn blue">+ New Department</div></a>

	<div class="spacer px10"></div>

	<table id="departments" class="styled">
		<thead>
			<th>Name</th>
			<th>Employees</th>
			<th>Head</th>
			<th></th>
		</thead>
		<tbody>
			<?php $Departments = \App\Models\Department::orderBy('name', 'ASC')->get(); ?>
			@if (count($Departments) === 0)
				<tr>
					<td colspan="4">There are no departments to display.</td>
				</tr>
			@else
				@foreach($Departments as $Department)
					<tr>
						<td style="font-weight:bold" class="text-center">{{ $Department -> name }}</td>
						<td class="text-center">{{ number_format(DB::select('SELECT COUNT(*) AS count FROM `employees` WHERE `job_id` IN (SELECT `id` FROM department_jobs WHERE `department_id`=?)', [$Department -> id])[0]->count) }}</td>
						<td class="text-center">
							@if (isset($Department -> head_of_department_id) && $Department -> head_of_department_id !== null)
								@component('partials.employee_profile')
									@slot('employee_id', $Department -> head_of_department_id)
									@slot('link', true)
								@endcomponent
							@else
								(none)
							@endif
						</td>
						<td class="text-center">
							<a href="/management/departments/edit/{{ $Department -> id }}"><div class="btn blue">Edit</div></a>
						</td>
					</tr>
				@endforeach
			@endif
		</tbody>
	</table>
@endsection

@section('css-includes')
	<style type="text/css">
		#departments .profile {
			display: inline-flex;
		}
	</style>
@endsection