@extends('layout')

@if ($Department == null)
	@section('content-box-title', 'New Department')
@else
	@section('content-box-title', 'Edit Department')
@endif
@section('content-box-icon', 'fas fa-users')
@section('content-box-back', '/management/departments')
@section('content-box')
	<form method="POST">
		<h2>Department</h2>
		<table>
			<tr>
				<td><label for="department_name">Name:</label></td>
				<td><input type="text" name="department_name" id="department_name" value="{{ $Department -> name }}" required/></td>
			</tr>
			<tr>
				<td><label for="head_of_department">Head of Department: </label></td>
				<td><input type="text" name="head_of_department" id="head_of_department" class="employee-selector" data-employee-id="{{ $Department -> head_of_department_id }}" value="{{ $Department -> head_of_department_id !== null ? $Department -> head_of_department -> full_name() : '' }}" required/></td>
			</tr>
		</table>
		
		<h2>Jobs</h2>
		
		<div class="btn blue" id="add-new-job">+ Add New</div>
		
		<div class="spacer px10"></div>
		
		<?php
			$Jobs = [];
			if ($Department != null) {
				$Jobs = \App\Models\Job::where('department_id', $Department -> id)->orderBy('title', 'ASC')->get();
			}
		?>
		<table class="styled {{ count($Jobs) === 0 ? '' : 'has-results' }}" id="jobs-table">
			<thead>
				<th style="width:100%">Title</th>
				<th style="width:0;padding:0"></th>
			</thead>
			<tbody>
				<tr class="row-template">
					<td></td>
					<td style="white-space:nowrap">
						<span class="edit-job btn blue">Edit</span>
						<span class="delete-job btn red">Delete</span>
					</td>
				</tr>
				<tr class="no-results">
					<td colspan="2">No jobs to show</td>
				</tr>
				@foreach($Jobs as $Job)
					<tr class="job" data-job-id="{{ $Job -> id }}">
						<td>{{ $Job -> title }}</td>
						<td style="white-space:nowrap">
							<span class="edit-job btn blue">Edit</span>
							<span class="delete-job btn red">Delete</span>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		
		@if ($Department != null)
			<input type="hidden" name="edit_department_id" id="edit_department_id" value="{{ $Department -> id }}"/>
		@endif
		
		<div class="spacer px20"></div>
		<div class="h-center"><div class="btn green wide disabled" id="save-btn">Save</div></div>
	</form>
@endsection

@section('js-includes')
	<script type="text/javascript" src="/assets/js/department.edit.js"></script>
@endsection