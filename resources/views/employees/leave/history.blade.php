@extends('employees.leave.layout')

@section('leave-content')
	<table class="styled h-center" id="leave-history">
		<thead>
			<tr>
				<th>Requested</th>
				<th>Approved</th>
				<th>Reason</th>
				<th>From</th>
				<th>To</th>
				<th>Notes</th>
			</tr>
		</thead>
		<tbody>
			@if (count($Leaves) === 0)
				<tr>
					<td colspan="6">No leaves to show</td>
				</tr>
			@else
				@foreach($Leaves as $Leave)
					<tr data-id="{{ $Leave -> id }}">
						<td>@component('partials.timestamp') @slot('timestamp', $Leave -> requested) @endcomponent</td>
						<td>
							@if ($Leave -> approved == 1)
								<i class="fas fa-check"></i>
							@elseif ($Leave -> approved == 2)
								<i class="fas fa-times"></i>
							@else
								<i class="far fa-hourglass"></i>
							@endif
							
							@if ($UserEmployee -> is_human_resources())
								<div class="spacer px10"></div>
								@if ($Leave -> approved == 0)
									@component('partials.button')
										@slot('text', 'Approve')
										@slot('color', 'green')
										@slot('icon', 'fas fa-check')
										@slot('small', true)
										@slot('class', 'approve')
									@endcomponent
									<div class="spacer px5"></div>
									@component('partials.button')
										@slot('text', 'Reject')
										@slot('color', 'red')
										@slot('icon', 'fas fa-times')
										@slot('small', true)
										@slot('class', 'unapprove')
									@endcomponent
								@elseif ($Leave -> approved == 1)
									@component('partials.button')
										@slot('text', 'Unapprove')
										@slot('color', 'red')
										@slot('icon', 'fas fa-times')
										@slot('small', true)
										@slot('class', 'unapprove')
									@endcomponent
								@else
									@component('partials.button')
										@slot('text', 'Approve')
										@slot('color', 'green')
										@slot('icon', 'fas fa-check')
										@slot('small', true)
										@slot('class', 'approve')
									@endcomponent
								@endif
							@endif
						</td>
						<td>{{ \App\Enums\LeaveReason::GetReasonName($Leave -> reason) }}</td>
						<td>@component('partials.timestamp') @slot('timestamp', $Leave -> absent_from) @endcomponent</td>
						<td>@component('partials.timestamp') @slot('timestamp', $Leave -> absent_to) @endcomponent</td>
						<td style="text-align:left">
							@if ($Leave -> notes !== null)
								<div class="notes">{{ $Leave -> notes }}</div>
							@endif
						</td>
					</tr>
				@endforeach
			@endif
		</tbody>
	</table>
@endsection

@section('js-includes')
	<script type="text/javascript" src="/assets/js/leave.js"></script>
@endsection