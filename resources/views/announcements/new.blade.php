@extends('layout')

@section('content-box-title', 'New Announcement')
@section('content-box-icon', 'fas fa-bullhorn')
@section('content-box')
	<form method="POST" class="h-center">
		@csrf
		
		<label>Important: <input type="checkbox" name="important"/></label>
		
		<div class="spacer px5"></div>
		
		<label>Title: <input type="text" name="title" required/></label>
		
		<div class="spacer px10"></div>
		
		<textarea name="body" style="width:250px;height:100px" required></textarea>
		
		<div class="spacer px10"></div>
		
		@component('partials.button')
			@slot('color', 'green')
			@slot('icon', 'fas fa-bullhorn')
			@slot('text', 'Announce')
			@slot('submit', true)
			@slot('name', 'announce')
		@endcomponent
	</form>
@endsection