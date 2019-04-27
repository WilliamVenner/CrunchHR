<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KeycardController extends Controller
{
	// Deletes any previously stored authentication token hashes
	// Generates authentication token hash salted by employee's password and current UNIX timestamp
	// Returns new authentication token hash to client
	public function GetToken($email)
	{
		$Employee = \App\Models\Employee::where('email', $email)->first();
		if (!$Employee) {
			return abort(404);
		}

		$User = DB::table('users')->where(['email' => $email])->first();
		if (!$User) {
			return abort(404);
		}

		$token = md5($User->password.time());

		DB::table('keycard_logins')->where('employee_id', $Employee->id)->delete();
		DB::table('keycard_logins')->insert([
			'employee_id' => $Employee->id,
			'token'       => $token,
		]);

		return response($token);
	}

	// Cancels a keycard login by deleting its authentication token
	public function CancelToken($token)
	{
		DB::table('keycard_logins')->where(['token' => $token])->delete();
	}

	// Checks whether keycard login has been authenticated
	// If authenticated, redirect client to cookie-setting URL
	// If not authenticated, return 0 to client
	public function CheckAuthentication($token)
	{
		$KeycardLogin = \App\Models\KeycardLogin::where('token', $token)->first();
		if (!$KeycardLogin) {
			return abort(404);
		}

		if ($KeycardLogin->authenticated !== null) {
			if (time() - $KeycardLogin->authenticated > 15) {
				DB::table('keycard_logins')->where(['token' => $token])->delete();

				return abort(404);
			} else {
				return response('1');
			}
		} else {
			return response('0');
		}
	}

	// Authenticates keycard login and informs client
	public function Authenticate($secret_key)
	{
		$Keycard = \App\Models\Keycard::where('secret_key', $secret_key)->first();
		if (!$Keycard) {
			return abort(404);
		}

		$KeycardLogin = \App\Models\KeycardLogin::where('employee_id', $Keycard->employee_id)->first();
		if ($KeycardLogin) {
			$KeycardLogin->authenticated = time();
			$KeycardLogin->save();
		}

		$Keycard->last_used = DB::raw('NOW()');
		$Keycard->last_used_ip_address = $_SERVER['REMOTE_ADDR'];
		$Keycard->save();

		return view('keycard.authenticated');
	}

	// Sets cookies to log in user and redirects user to home page
	public function LoginKeycardUser($token_and_remember)
	{
		$token = substr($token_and_remember, 0, -1);
		$remember = substr($token_and_remember, -1, 1) == '1' ? true : false;

		$KeycardLogin = \App\Models\KeycardLogin::where('token', $token)->first();
		if (!$KeycardLogin) {
			return view('keycard.expired');
		}

		if ($KeycardLogin->authenticated !== null) {
			if (time() - $KeycardLogin->authenticated > 15) {
				DB::table('keycard_logins')->where(['token' => $token])->delete();

				return view('keycard.expired');
			} else {
				$Employee = \App\Models\Employee::where('id', $KeycardLogin->employee_id)->first();
				$User = DB::table('users')->where(['email' => $Employee->email])->first();
				$KeycardLogin->delete();

				Auth::loginUsingId($User->id, $remember);

				return redirect('/');
			}
		} else {
			return view('keycard.expired');
		}
	}

	public function EmployeeKeycardSettings(Request $request, $employee_id = null)
	{
		$Employee = null;
		if ($employee_id === null) {
			$Employee = $request->get('UserEmployee');
		} else {
			$Employee = \App\Models\Employee::where('id', $employee_id)->first();
		}
		if ($Employee === null) {
			return abort(404, 'Employee not found');
		}

		$Keycard = \App\Models\Keycard::where('employee_id', $Employee->id)->first();

		return view('employees.keycard', ['Employee' => $Employee, 'EmployeeURLID' => $employee_id === null ? 'me' : $employee_id, 'Keycard' => $Keycard]);
	}

	public function ControlKeycard(Request $request, $employee_id = null)
	{
		$Employee = null;
		if ($employee_id === null) {
			$Employee = $request->get('UserEmployee');
		} else {
			$Employee = \App\Models\Employee::where('id', $employee_id)->first();
		}
		if ($Employee === null) {
			return abort(404, 'Employee not found');
		}

		$Keycard = \App\Models\Keycard::where('employee_id', $Employee->id)->first();
		if (isset($_POST['revoke']) && $Keycard !== null) {
			$Keycard->delete();
			$Keycard = null;

			\App\Notifications::NotifyManagement('An employee has revoked their keycard', 'Please verify whether this was an authorized action', '/employees/'.$Employee->id.'/keycard');
		} elseif (isset($_POST['create']) && $Keycard === null) {
			$User = DB::table('users')->where(['email' => $Employee->email])->first();

			$Keycard = new \App\Models\Keycard();
			$Keycard->employee_id = $Employee->id;
			$Keycard->secret_key = md5('GENERATE_KEYCARD_'.time().$User->password);
			$Keycard->save();

			\App\Notifications::NotifyManagement('An employee has generated a new keycard', 'Please verify whether this was an authorized action', '/employees/'.$Employee->id.'/keycard');
		}

		return redirect('/'.$request->path());
	}
}
