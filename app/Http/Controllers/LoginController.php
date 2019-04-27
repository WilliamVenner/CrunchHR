<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
	public function Login()
	{
		if (Auth::check()) {
			return redirect('/');
		} else {
			return view('auth.login');
		}
	}

	public function Logout()
	{
		if (Auth::check()) {
			Auth::logout();
		}

		return redirect('/auth/login');
	}

	public function Authenticate()
	{
		if (!isset($_POST['email'], $_POST['password'], $_POST['g-recaptcha-response'])) {
			return abort(400);
		}
		if (!\App\Helpers::Verify_reCAPTCHA($_POST['g-recaptcha-response'])) {
			return view('auth.login', ['reCAPTCHA' => false]);
		}
		if (Auth::attempt(['email' => $_POST['email'], 'password' => $_POST['password']], isset($_POST['remember_me']) && $_POST['remember_me'] == 'on')) {
			return redirect('/');
		} else {
			return view('auth.login', ['failed' => true]);
		}
	}

	public function HasSignupCode()
	{
		if (!isset($_POST['email'])) {
			return abort(400);
		}

		return response(count(DB::select('SELECT NULL FROM employees WHERE `email`=? AND `signup_code` IS NOT NULL LIMIT 1', [$_POST['email']])) === 0 ? '0' : '1');
	}

	public function VerifySignupCode()
	{
		if (!isset($_POST['email'], $_POST['signup_code'])) {
			return abort(400);
		}
		$Employee = \App\Models\Employee::where(['email' => $_POST['email'], 'signup_code' => $_POST['signup_code']])->first();
		if ($Employee) {
			return \App\Helpers::JSONResponse([
				'name' => $Employee->first_name,
				'picture' => $Employee->picture(),
			]);
		} else {
			return response('0');
		}
	}

	public function Signup()
	{
		if (isset($_POST['signup-password'], $_POST['signup-confirm-password'], $_POST['signup-email'], $_POST['signup-code'])) {
			if ($_POST['signup-password'] === $_POST['signup-confirm-password'] && strlen($_POST['signup-password']) >= 8) {
				$number_check = [];
				preg_match('/\d/', $_POST['signup-password'], $number_check);
				if (count($number_check) === 0) {
					return abort(400);
				}

				$Employee = \App\Models\Employee::where(['email' => $_POST['signup-email'], 'signup_code' => $_POST['signup-code']])->first();
				if (!$Employee) {
					return abort(403);
				}

				$User = \App\User::create([
					'email'    => $_POST['signup-email'],
					'password' => Hash::make($_POST['signup-password']),
				]);

				$Employee->signup_code = null;
				$Employee->save();

				Auth::login($User);

				return redirect('/');
			}
		}

		return abort(400);
	}
}
