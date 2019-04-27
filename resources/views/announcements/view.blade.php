@extends('layout')

@section('content-header', 'Announcements')

@section('content')
	@if (count($Announcements) === 0)
		<div style="text-align:center">There are no announcements to display.</div>
	@else
		@foreach($Announcements as $Announcement)
			<div class="announcement {{ $Announcement -> important == 1 ? 'important' : '' }}">
				<div class="icon">
					@if ($Announcement -> important == 1)
						<i class="fas fa-exclamation-triangle"></i>
					@else
						<i class="fas fa-bullhorn"></i>
					@endif
				</div>
				<div class="content">
					@component('partials.employee_profile')
						@slot('link', true)
						@slot('employee_id', $Announcement -> employee_id)
						@slot('small', true)
					@endcomponent
					<div class="title">{{ $Announcement -> title }}</div>
					<div class="description">{{ $Announcement -> contents }}</div>
					<div class="created">@component('partials.timestamp') @slot('timestamp', $Announcement -> created_at) @endcomponent</div>
				</div>
			</div>
		@endforeach
	@endif
@endsection