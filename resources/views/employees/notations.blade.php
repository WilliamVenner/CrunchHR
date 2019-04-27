@extends('employees.view')

@section('title', $Employee -> full_name() . ' · Notations')

@section('employee-content-center')
	<form method="POST">
		@csrf
		
		<input type="hidden" name="employee_id" value="{{ $Employee -> id }}"/>
		@component('partials.messagebox')
			@slot('employee_id', $UserEmployee -> id)
			@slot('placeholder', 'New note...')
			@slot('writing', true)
		@endcomponent
	</form>
	@if (count($Notations) === 0)
		<div class="spacer px15"></div>
		<span class="no-notations">There are no notations to show</span>
	@else
		<div class="spacer px15"></div>
		<form method="POST">
			@csrf
			
			@foreach($Notations as $Notation)
				@component('partials.messagebox')
					@slot('id', $Notation -> id)
					@slot('employee_id', $Notation -> author_id)
					@slot('content', $Notation -> content)
					@slot('delete', true)
					@slot('created_at', $Notation -> created_at)
				@endcomponent
			@endforeach
		</form>
	@endif
@endsection