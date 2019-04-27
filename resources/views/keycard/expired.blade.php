@extends('keycard.layout')

@section('content')
	<i class="fas fa-ban" id="expired"></i>
	
	<h2>Uh oh!</h2>
	
	Looks like your keycard login request has expired. Please try again.
	
	<div class="spacer px10"></div>
	<a href="/auth/login">Try Again</a>
@endsection