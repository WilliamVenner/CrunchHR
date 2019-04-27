@extends('employees.leave.layout')

@section('leave-content')
	<form method="POST">
		@csrf
		
		<label>Leave reason: <select name="reason" required>
			<option value="1">Illness</option>
			<option value="2">Physical Health</option>
			<option value="3">Mental Health</option>
			<option value="4">Work Event</option>
			<option value="5">Non-Work Event</option>
			<option value="6">Maternity/Paternity</option>
			<option value="7">Childcare/Eldercare/Carer</option>
			<option value="8">Holiday</option>
			<option value="0">Other</option>
		</select></label>
		
		<div class="spacer px10"></div>
		
		<label for="notes">Notes:</label><div class="spacer px5"></div>
		<textarea name="notes" style="width:350px;height:100px"></textarea>
		
		<div class="spacer px10"></div>
		
		<label>From: <input type="date" name="from" value="{{ date('Y-m-d') }}" id="from" required/></label>
		<label>To: <input type="date" name="to" id="to" required/></label>
		
		<div class="spacer px10"></div>
		
		@component('partials.button')
			@slot('color', 'green')
			@slot('text', 'Request Leave')
			@slot('icon', 'fas fa-sign-out-alt')
			@slot('name', 'request-leave')
			@slot('submit', true)
		@endcomponent
	</form>
@endsection

@section('js-includes')
	<script type="text/javascript" src="/assets/js/leave.js"></script>
@endsection