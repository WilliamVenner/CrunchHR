@extends('employees.view')

@section('title', $Employee -> full_name() . ' · One to Ones')

@section('employee-content-center')
	<table class="styled" style="text-align:center">
		<thead>
			<tr>
				<th>#</th>
				<th>Priority</th>
				<th>Created</th>
				<th>Due</th>
				<th>Updated</th>
				<th>Unread</th>
				<th>Locked</th>
				<th>Messages</th>
				<th>Subject</th>
			</tr>
		</thead>
		<tbody>
			@foreach($OneToOnes as $OneToOne)
				<tr>
					<td>{{ $OneToOne -> id }}</td>
					<td>
						@switch($OneToOne -> priority)
							@case(0)
								Very Low
								@break
							@case(1)
								Low
								@break
							@case(2)
								Normal
								@break
							@default
								Very High
						@endswitch
					</td>
					<td>@component('partials.timestamp') @slot('timestamp', $OneToOne -> created_at) @endcomponent</td>
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
					<td>{{ number_format($OneToOne -> messages) }}</td>
					<td><a href="/1to1/{{ $OneToOne -> id }}">{{ $OneToOne -> subject }}</a></td>
				</tr>
			@endforeach
		</tbody>
	</table>
@endsection