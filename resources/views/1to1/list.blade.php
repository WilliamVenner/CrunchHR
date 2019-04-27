@extends('layout')

@section('content-box-title', 'One to Ones')
@section('content-box-icon', 'fas fa-comments')
@section('content-box')
	@component('partials.button')
		@slot('color', 'green')
		@slot('text', 'New One to One')
		@slot('icon', 'fas fa-plus')
		@slot('link', '/hr/1to1/new')
	@endcomponent
	
	<div class="spacer px15"></div>
	
	<form method="POST">
		@csrf
			
		<label><input type="checkbox" name="hidelocked" {{ isset($_POST['hidelocked']) && $_POST['hidelocked'] === 'on' ? 'checked' : '' }}/> Hide Locked</label>
		<br>
		<label><input type="checkbox" name="hideread" {{ isset($_POST['hideread']) && $_POST['hideread'] === 'on' ? 'checked' : '' }}/> Hide Read</label>
		
		<div class="spacer px10"></div>
		
		@component('partials.button')
			@slot('color', 'blue')
			@slot('text', 'Apply Filter')
			@slot('icon', 'fas fa-filter')
			@slot('submit', true)
			@slot('small', true)
		@endcomponent
		
		<div class="spacer px15"></div>
		
		<table class="styled" style="text-align:center">
			<thead>
				<tr>
					<th>#</th>
					<th>Priority</th>
					<th>Created</th>
					<th>With</th>
					<th>Due</th>
					<th>Updated</th>
					<th>Unread</th>
					<th>Locked</th>
					<th>Messages</th>
					<th>Subject</th>
				</tr>
			</thead>
			<tbody>
				@if (count($OneToOnes) === 0)
					<tr>
						<td colspan="10">No results found</td>
					</tr>
				@else
					@foreach($OneToOnes as $OneToOne)
						<tr>
							<td>{{ $OneToOne -> id }}</td>
							<td>
								@switch($OneToOne -> priority)
									@case(0)
										Low
										@break
									@case(1)
										Normal
										@break
									@case(2)
										High
										@break
									@case(3)
										Very High
										@break
									@default
										Very High
								@endswitch
							</td>
							<td>@component('partials.timestamp') @slot('timestamp', $OneToOne -> created_at) @endcomponent</td>
							<td>@component('partials.employee_profile') @slot('employee_id', $OneToOne -> employee_id) @slot('link', true) @endcomponent</td>
							<td>@component('partials.timestamp') @slot('timestamp', $OneToOne -> due) @endcomponent</td>
							<td>@component('partials.timestamp') @slot('timestamp', $OneToOne -> last_updated) @endcomponent</td>
							<td>
								@if ($OneToOne -> unread == 1)
									<i class="fas fa-envelope"></i>
								@else
									<i class="fas fa-envelope-open-text"></i>
								@endif
							</td>
							<td class="text-center">
								@if ($OneToOne -> locked == 1)
									<i class="fas fa-lock"></i>
								@else
									<i class="fas fa-unlock"></i>
								@endif
							</td>
							<td>{{ $OneToOne -> messages }}</td>
							<td><a href="/1to1/{{ $OneToOne -> id }}">{{ $OneToOne -> subject }}</a></td>
						</tr>
					@endforeach
				@endif
			</tbody>
		</table>
		
		<br>
		@component('partials.pagination')
			@slot('pages', $pages)
		@endcomponent
	</form>
@endsection