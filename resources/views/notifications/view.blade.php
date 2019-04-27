@extends('layout')

@section('title', 'Notifications')
@section('content-header', 'Notifications')

@section('css-includes')
	<link rel="stylesheet" href="/assets/css/notifications.view.css" type="text/css" />
@endsection

@section('content')
	<div style="text-align:center">
		@component('partials.button')
			@slot('id', 'subscription-toggle')
			@slot('color', 'green')
			@slot('icon', 'fas fa-bell')
			@slot('text', 'Subscribe')
			@slot('style', 'min-width:150px')
			@slot('disabled', true)
		@endcomponent
		
		@component('partials.button')
			@slot('id', 'delete-all')
			@slot('color', 'red')
			@slot('icon', 'fas fa-trash-alt')
			@slot('text', 'Delete All')
			@slot('style', 'min-width:150px')
			@if (count($notifications) === 0)
				@slot('disabled', true)
			@endif
		@endcomponent
		
		<div class="spacer px20"></div>
		
		@if (count($notifications) > 0)
			<div id="notifications">
				@foreach($notifications as $Notification)
					<div class="notification {{ $Notification -> seen == 0 ? 'unread' : '' }}" data-id="{{ $Notification -> id }}">
						@if ($Notification -> link != null)
							<a href="/notifications/{{ $Notification -> id }}">
						@endif
						<div class="content">
							<div class="title">{{ $Notification -> title }}</div>
							<div class="body">
								<span>{{ $Notification -> content }}</span>
								@component('partials.timestamp') @slot('timestamp', $Notification -> created_at) @endcomponent
							</div>
						</div>
						@if ($Notification -> link != null)
							</a>
						@endif
						<div class="delete"><i class="fas fa-times"></i></div>
					</div>
				@endforeach
			</div>
			<div id="no-notifications">You have no notifications to view.</div>
		@else
			You have no notifications to view.
		@endif
	</div>
@endsection

@section('js-includes')
	<script type="text/javascript" src="/assets/js/notifications.view.js"></script>
@endsection

<?php
	DB::table('notifications')->where(['employee_id' => $UserEmployee -> id])->update(['viewed' => '1']);
	DB::table('notifications')->where(['employee_id' => $UserEmployee -> id, 'link' => null])->update(['seen' => '1']);
?>