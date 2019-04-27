<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
	public function GetEmployeeAvatar($hash)
	{
		$pathinfo = pathinfo($hash);
		$ext = $pathinfo['extension'];
		$hash = $pathinfo['filename'];

		if ($ext !== 'png' && $ext !== 'jpg' && $ext !== 'jpeg') {
			return abort(404);
		}
		$ProfilePicture = \App\Models\ProfilePicture::where('hash', $hash)->first();
		if (!$ProfilePicture) {
			return abort(404);
		}

		if ($ext === 'png') {
			if ($ProfilePicture->png !== null) {
				return response($ProfilePicture->png)->header('Content-Type', 'image/png');
			} else {
				return response($ProfilePicture->jpg)->header('Content-Type', 'image/jpeg');
			}
		} elseif ($ext === 'jpg' || $ext === 'jpeg') {
			return response($ProfilePicture->jpg)->header('Content-Type', 'image/jpeg');
		}
	}

	public function EditEmployee(Request $request, $employee_id = null)
	{
		$UserEmployee = $request->get('UserEmployee');
		if (!isset($_POST['first_name'], $_POST['last_name'])) {
			return abort(400);
		}
		if (isset($_POST['address_1'])) {
			if (!isset($_POST['city'], $_POST['county'], $_POST['postcode'], $_POST['country'])) {
				return abort(400);
			}
		}

		$Employee;
		if ($employee_id === null) {
			$Employee = new \App\Models\Employee();
			$Employee->signup_code = rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9);
		} elseif ($employee_id === 'me') {
			$Employee = $UserEmployee;
		} else {
			$Employee = \App\Models\Employee::where('id', $employee_id)->first();
			if (!$Employee) {
				return abort(404);
			}
		}
		
		DB::table('users')->where(['email' => $Employee->email])->update(['email' => $_POST['email']]);
		$Employee->email = $_POST['email'];
		$Employee->first_name = $_POST['first_name'];
		$Employee->middle_name = isset($_POST['middle_name']) && !empty($_POST['middle_name']) ? $_POST['middle_name'] : null;
		$Employee->last_name = $_POST['last_name'];
		$Employee->mobile = isset($_POST['mobile']) && !empty($_POST['mobile']) ? str_replace(' ', '', $_POST['mobile']) : null;
		$Employee->mobile_ext = isset($_POST['mobile_ext']) && !empty($_POST['mobile_ext']) ? $_POST['mobile_ext'] : null;
		$Employee->landline = isset($_POST['landline']) && !empty($_POST['landline']) ? str_replace(' ', '', $_POST['landline']) : null;
		$Employee->landline_ext = isset($_POST['landline_ext']) && !empty($_POST['landline_ext']) ? $_POST['landline_ext'] : null;
		if ($UserEmployee->is_human_resources()) {
			if (isset($_POST['job_id']) && $_POST['job_id'] !== 'NULL') {
				$Job = \App\Models\Job::where('id', $_POST['job_id'])->first();
				if (!$Job) {
					return abort(400);
				}
			}
			$Employee->job_id = isset($_POST['job_id']) && !empty($_POST['job_id']) && $_POST['job_id'] !== 'NULL' ? $_POST['job_id'] : null;
		}

		if (isset($_POST['address_1']) && !empty($_POST['address_1'])) {
			$Employee->address_1 = $_POST['address_1'];
			$Employee->address_2 = isset($_POST['address_2']) && !empty($_POST['address_2']) ? $_POST['address_2'] : null;
			$Employee->city = $_POST['city'];
			$Employee->county = $_POST['county'];
			$Employee->postcode = strtoupper(str_replace(' ', '', $_POST['postcode']));
			$Employee->country = $_POST['country'];
		} else {
			$Employee->address_1 = null;
			$Employee->address_2 = null;
			$Employee->city = null;
			$Employee->county = null;
			$Employee->postcode = null;
			$Employee->country = null;
		}

		if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
			if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
				return abort(400);
			}
			if ($_FILES['avatar']['type'] !== 'image/png' && $_FILES['avatar']['type'] !== 'image/jpeg') {
				return abort(400);
			}
			if ($_FILES['avatar']['size'] > 2000000) {
				return abort(400);
			}

			DB::table('profile_pictures')->where('employee_id', $Employee->id)->delete();

			$content = file_get_contents($_FILES['avatar']['tmp_name']);

			$ProfilePicture = new \App\Models\ProfilePicture();
			$ProfilePicture->employee_id = $Employee->id;
			if ($_FILES['avatar']['type'] === 'image/jpeg') {
				$ProfilePicture->jpg = $content;
			} else {
				$ProfilePicture->png = $content; // pngs are too high quality to be displayed as icons, so we will additionally convert the png to a jpg for the compression

				$stream = fopen('php://memory', 'w+'); // stream jpg to memory so we don't need to write to disk/output buffer

				$png_img = imagecreatefrompng($_FILES['avatar']['tmp_name']);
				$bg = imagecreatetruecolor(imagesx($png_img), imagesy($png_img)); // add a white background to replace png alpha channel
				imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255)); // fill it with white
				imagealphablending($bg, true); // make sure the image itself is layered above the white background
				imagecopy($bg, $png_img, 0, 0, 0, 0, imagesx($png_img), imagesy($png_img)); // add the image onto the white background
				imagedestroy($png_img); // free the png image from memory; we don't need it anymore
				imagejpeg($bg, $stream, 95); // generate the jpg and write it to the stream
				rewind($stream); // set stream writing pointer back to the beginning to prevent interfering with other streams
				imagedestroy($bg); // free the white background from memory; we don't need it anymore

				$ProfilePicture->jpg = stream_get_contents($stream); // add our new jpg to the database!
			}
			$ProfilePicture->save();

			$ProfilePicture->hash = md5(env('EMPLOYEE_PICTURE_HASH_SALT').$ProfilePicture->id); // generate the URL hash from the auto_increment id
			$ProfilePicture->save();
		}

		$Employee->save();

		return redirect('/employees/'.($employee_id === null ? $Employee->id : $employee_id));
	}

	public function EmployeeSelector()
	{
		if (!isset($_POST['search_for']) || empty(trim($_POST['search_for']))) {
			return abort(400);
		}

		$search_for_split = explode(' ', trim(strtolower($_POST['search_for'])));
		$search_for_split_len = count($search_for_split);

		$employee_names = DB::table('employees')->select('id', 'first_name', 'middle_name', 'last_name')->get();

		$scoreboard = [];

		global $leaderboard, $leaderboard_employees;
		$leaderboard = [0, 0, 0, 0, 0, 0, 0];
		$leaderboard_employees = [null, null, null, null, null, null, null];

		// Recursively sorts the leaderboard as scores are recorded
		// Somewhat similar to a quick sort I think?
		function RecordScore($employee_id, $score, $left = 0, $right = null)
		{
			global $leaderboard, $leaderboard_employees;

			if ($right === null) {
				$right = count($leaderboard) - 1;
			}

			$left_item = $leaderboard[$left];
			$right_item = $leaderboard[$right];

			if ($score >= $left_item) {
				array_splice($leaderboard, $left, 0, $score);
				array_splice($leaderboard_employees, $left, 0, $employee_id);
				array_pop($leaderboard);
				array_pop($leaderboard_employees);
			} elseif ($left != $right && $score > $right_item) {
				$left += 1;
				$right -= 1;
				RecordScore($employee_id, $score, $left, $right);
			} else {
				array_splice($leaderboard, $right + 1, 0, $score);
				array_splice($leaderboard_employees, $right + 1, 0, $employee_id);
				array_pop($leaderboard);
				array_pop($leaderboard_employees);
			}
		}

		// Calculates a score of how similar the user's input is to the comparison string
		function SimilarityScore($input, $compare)
		{
			$score = 0;
			for ($i = 0; $i < strlen($input); $i++) {
				if (!isset($compare[$i])) {
					break;
				}
				if ($input[$i] === $compare[$i]) {
					$score += 1;
				} else {
					break;
				}
			}

			return $score / strlen($input);
		}

		// Where n = search_for_split and #n = search_for_split_len

		// If the user only supplies ONE name:
		// First name     == (SimilarityScore(n[0], FirstName) * 3) points
		// Last name      == (SimilarityScore(n[0], LastName) * 2) points
		// Middle name(s) == MEAN(SimilarityScore(n[0], MiddleName[*])) points

		// If the user supplies TWO names:
		// First name     == (SimilarityScore(n[0], FirstName) * 2) points
		// Last name      == (SimilarityScore(n[1], LastName) * 2) points
		// Middle name(s) == MEAN(SimilarityScore(n[*], MiddleName[*])) points

		// If the user supplies THREE or more names:
		// First name     == (SimilarityScore(n[0], FirstName) * 2) points
		// Last name      == (SimilarityScore(n[#n - 1], LastName) * 2) points
		// Middle name(s) == MEAN(SimilarityScore(n[*], MiddleName[*])) points

		switch ($search_for_split_len) {
			case 1:
				foreach ($employee_names as $employee) {
					$score = 0;
					$score += SimilarityScore($search_for_split[0], strtolower($employee->first_name)) * 3;
					$score += SimilarityScore($search_for_split[0], strtolower($employee->last_name)) * 2;

					if ($employee->middle_name !== null) {
						$middle_name_score = 0;
						$middle_names = explode(' ', strtolower($employee->middle_name));
						foreach ($middle_names as $middle_name) {
							$middle_name_score += SimilarityScore($search_for_split[0], $middle_name);
						}
						$score += $middle_name_score / count($middle_names);
					}

					if ($score > 0) {
						$scoreboard[$employee->id] = (isset($scoreboard[$employee->id]) ? $scoreboard[$employee->id] + $score : $score);
						RecordScore($employee->id, $scoreboard[$employee->id]);
					}
				}
				break;

			case 2:
				foreach ($employee_names as $employee) {
					$score = 0;
					$score += SimilarityScore($search_for_split[0], strtolower($employee->first_name)) * 2;
					$score += SimilarityScore($search_for_split[1], strtolower($employee->last_name)) * 2;

					if ($employee->middle_name !== null) {
						$middle_name_score = 0;
						$middle_names = explode(' ', strtolower($employee->middle_name));
						foreach ($middle_names as $middle_name) {
							$middle_name_score += (SimilarityScore($search_for_split[0], $middle_name) + SimilarityScore($search_for_split[1], $middle_name)) / 2;
						}
						$score += $middle_name_score / count($middle_names);
					}

					if ($score > 0) {
						$scoreboard[$employee->id] = (isset($scoreboard[$employee->id]) ? $scoreboard[$employee->id] + $score : $score);
						RecordScore($employee->id, $scoreboard[$employee->id]);
					}
				}
				break;

			default:
				foreach ($employee_names as $employee) {
					$score = 0;
					$score += SimilarityScore($search_for_split[0], strtolower($employee->first_name)) * 2;
					$score += SimilarityScore($search_for_split[$search_for_split_len - 1], strtolower($employee->last_name)) * 2;

					if ($employee->middle_name !== null) {
						$total_middle_name_score = 0;
						$middle_names = explode(' ', strtolower($employee->middle_name));
						foreach ($middle_names as $middle_name) {
							$middle_name_score = 0;
							for ($i = 1; $i < $search_for_split_len - 1; $i++) {
								$middle_name_score += SimilarityScore($search_for_split[$i], $middle_name);
							}
							$total_middle_name_score += $middle_name_score / ($search_for_split_len - 2);
						}
						$score += $total_middle_name_score / count($middle_names);
					}

					if ($score > 0) {
						$scoreboard[$employee->id] = (isset($scoreboard[$employee->id]) ? $scoreboard[$employee->id] + $score : $score);
						RecordScore($employee->id, $scoreboard[$employee->id]);
					}
				}
		}

		if (!empty($scoreboard)) {
			$result = [];
			$total_score = 0;
			foreach ($leaderboard as $k => $score) {
				$total_score += $score;
			}
			foreach ($leaderboard_employees as $k => $employee_id) {
				if ($employee_id !== null) {
					$Employee = \App\Models\Employee::where('id', $employee_id)->first();
					$result[] = [
						'id'          => $employee_id,
						'hash'        => $Employee->picture_hash(),
						'first_name'  => $Employee->first_name,
						'middle_name' => $Employee->middle_name,
						'last_name'   => $Employee->last_name,
						'relevance'   => round(($leaderboard[$k] / $total_score) * 100),
					];
				}
			}

			return \App\Helpers::JSONResponse($result);
		} else {
			return \App\Helpers::JSONResponse([]);
		}
	}

	public function ViewDetails(Request $request, $employee_id = 'me')
	{
		$UserEmployee = $request->get('UserEmployee');
		$Employee = $employee_id === 'me' ? $UserEmployee : \App\Models\Employee::where('id', $employee_id)->first();
		if (!$Employee) {
			return abort(404, 'Employee does not exist');
		}
		
		if ($employee_id !== 'me' && $Employee->id == $UserEmployee->id) return redirect('/employees/me');
		
		if ($Employee->id == $UserEmployee->id || $UserEmployee->is_human_resources()) {
			return view('employees.editdetails', ['Employee' => $Employee, 'EmployeeURLID' => $employee_id]);
		} else {
			return view('employees.showprofile', ['Employee' => $Employee, 'EmployeeURLID' => $employee_id]);
		}
	}

	public function ViewEmployees()
	{
		return view('employees.all');
	}
}
