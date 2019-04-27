@extends('layout')

@section('content-box-title', 'New One to One')
@section('content-box-icon', 'fas fa-comments')
@section('content-box-back', '/hr/1to1')
@section('content-box')
	<form method="POST">
		@csrf
		
		<label for="subject">Subject: <input type="text" required name="subject"/></label>
		
		<div class="spacer px10"></div>
		
		<label for="employee_id">Employee: <input type="text" required name="employee_id" class="employee-selector"/></label>
		
		<div class="spacer px10"></div>
		
		<label for="priority">Priority: <select name="priority">
			<option value="0">Low</option>
			<option selected value="1">Normal</option>
			<option value="2">High</option>
			<option value="3">Very High</option>
		</select></label>
		
		<div class="spacer px10"></div>
		
		<label for="priority">Due: <input type="date" name="due" required></label>
		
		<div class="spacer px10"></div>
		
		@component('partials.button')
			@slot('color', 'green')
			@slot('text', 'Create')
			@slot('submit', true)
			@slot('icon', 'fas fa-plus')
			@slot('name', 'submit')
		@endcomponent
	</form>
@endsection