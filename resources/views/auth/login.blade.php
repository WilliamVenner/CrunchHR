@extends('auth.layout')

@section('title', 'Login')
@section('content')
	<form method="POST">
		{{ csrf_field() }}
		<img src="/assets/img/employee.png" class="employee-picture"/>
		<div class="spacer px15"></div>
		<div id="auth-inputs">
			<input type="email" name="email" id="email" placeholder="Email address..." value="{{ isset($_POST['email']) ? $_POST['email'] : '' }}"/>
			<div class="spacer px10"></div>
			<input type="password" name="password" minlength="8" id="password" placeholder="Password..."/>
			<div class="spacer px10"></div>
			<button type="submit" class="btn green" id="login" disabled>Login</button>
			<div class="spacer px10"></div>
			@component('partials.button')
				@slot('color', 'purple')
				@slot('text', 'Keycard')
				@slot('icon', 'fas fa-id-card')
				@slot('id', 'keycard-login-btn')
			@endcomponent
			<div class="spacer px15"></div>
			@component('partials.reCAPTCHA') @endcomponent
		</div>
		
		<div class="spacer px10"></div>
		<label for="remember_me_checkbox" id="remember_me"><input type="checkbox" name="remember_me" id="remember_me_checkbox" {{ isset($_POST['remember_me']) && $_POST['remember_me'] == true ? 'checked' : '' }}/><span>Remember Me</span></label>
		
		@if (isset($reCAPTCHA) && $reCAPTCHA === false)
			<div class="spacer px15"></div>
			<div id="error" class="shake">Please complete the reCAPTCHA</div>
		@elseif (isset($failed) && $failed === true)
			<div class="spacer px15"></div>
			<div id="error" class="shake">Your email or password is incorrect</div>
		@endif
	</form>
@endsection

@section('post-content')
	<div id="signup-1">
		<div>
			<h3 style="margin:0">Welcome to CrunchHR!</h3>
			<div class="spacer px15"></div>
			The person that signed you up to the system should have provided you with your "Sign up code", please enter it below...
			
			<div class="spacer px20"></div>
			
			<div id="signup-code">
				<input type="text" maxlength="1"/>
				<input type="text" maxlength="1"/>
				<input type="text" maxlength="1"/>
				<input type="text" maxlength="1"/>
				<input type="text" maxlength="1"/>
				<input type="text" maxlength="1"/>
			</div>
			
			<div class="spacer px20"></div>
			<div class="btn red wide signup-cancel">Cancel</div>
		</div>
	</div>
	
	<div id="signup-2">
		<form method="POST" action="/auth/signup">
			{{ csrf_field() }}
			
			<input type="hidden" name="signup-email" id="signup-email-input"/>
			<input type="hidden" name="signup-code" id="signup-code-input"/>
			
			<img src="/assets/img/employee.png" class="employee-picture"/>
			
			<div class="spacer px10"></div>
			
			<h3 style="margin:0">Please enter your desired password, <span id="employee-name"></span></h3>
			<div class="spacer px15"></div>
			<span id="password-requirements">It should be at least 8 characters and contain at least 1 number.</span>
			
			<div class="spacer px15"></div>
			
			<input type="password" minlength="8" name="signup-password" id="signup-password" placeholder="Password..."/>
			<div class="spacer px10"></div>
			<input type="password" minlength="8" name="signup-confirm-password" id="signup-confirm-password" placeholder="Confirm Password..."/>
			
			<div class="spacer px15"></div>
			
			<input type="submit" class="btn green wide" disabled id="signup-btn" value="Sign up" name="finish-signup"/>
			<div class="spacer px10"></div>
			<div class="btn red wide signup-cancel">Cancel</div>
		</div>
	</div>
	
	<div id="keycard-login">
		<div>
			<h3 style="margin:0">Keycard Authentication</h3>
			
			<div class="spacer px10"></div>
			
			<span class="loading fas fa-spinner"></span>&nbsp;Scan your keycard using your smartphone now...
			
			<div class="spacer px20"></div>
			
			@component('partials.button')
				@slot('color', 'red')
				@slot('text', 'Cancel')
				@slot('icon', 'fas fa-times')
				@slot('id', 'keycard-login-cancel')
			@endcomponent
		</div>
	</div>
@endsection