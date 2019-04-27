@extends('layout')

@section('title', $OneToOne -> subject)
@section('content-header', $OneToOne -> subject)

@section('content')
	@if ($UserEmployee -> is_human_resources())
		<form method="POST" class="h-center">
			@csrf
			@if ($OneToOne -> locked == 1)
				@component('partials.button')
					@slot('text', 'Unlock')
					@slot('icon', 'fas fa-unlock')
					@slot('color', 'green')
					@slot('name', 'unlock')
					@slot('submit', true)
				@endcomponent
			@else
				@component('partials.button')
					@slot('text', 'Lock')
					@slot('icon', 'fas fa-lock')
					@slot('color', 'red')
					@slot('name', 'lock')
					@slot('submit', true)
				@endcomponent
			@endif
		</form>
		
		<div class="spacer px15"></div>
	@endif
	
	<table class="card">
		<tbody>
			<tr>
				<th>Created</th>
				<td>@component('partials.timestamp') @slot('timestamp', $OneToOne -> created_at) @endcomponent</td>
			</tr>
			<tr>
				<th>Last Updated</th>
				<td>@component('partials.timestamp') @slot('timestamp', $OneToOne -> last_updated) @endcomponent</td>
			</tr>
			<tr>
				<th>Employee</th>
				<td>@component('partials.employee_profile') @slot('employee_id', $OneToOne -> employee_id) @slot('link', true) @endcomponent</td>
			</tr>
			<tr>
				<th>Messages</th>
				<td>{{ $OneToOne -> messages }}</td>
			</tr>
			<tr>
				<th>Locked</th>
				<td class="text-center">
					@if ($OneToOne -> locked == 1)
						<i class="fas fa-lock"></i>
					@else
						<i class="fas fa-unlock"></i>
					@endif
				</td>
			</tr>
		</tbody>
	</table>
	
	@if ($OneToOne -> locked == 0)
		<div class="spacer px15"></div>
		
		<form method="POST">
			@csrf
			
			@component('partials.messagebox')
				@slot('employee_id', $UserEmployee -> id)
				@slot('placeholder', 'New message...')
				@slot('writing', true)
			@endcomponent
		</form>
	@endif
	
	<div class="spacer px15"></div>
	
	@foreach($OneToOneReplies as $OneToOneReply)
		@component('partials.messagebox')
			@slot('employee_id', $OneToOneReply -> employee_id)
			@slot('content', $OneToOneReply -> content)
			@slot('created_at', $OneToOneReply -> created_at)
		@endcomponent
	@endforeach
@endsection