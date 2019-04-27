<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
	public function ViewDepartments()
	{
		return view('departments.view');
	}

	public function EditDepartment($department_id = null)
	{
		if (isset($_POST['department_name'])) {
			DB::beginTransaction();

			$validation = (function () {
				if (!isset($_POST['jobs'])) {
					return [false];
				}

				$department_id = isset($_POST['department_id']) && intval($_POST['department_id']) ? intval($_POST['department_id']) : null;
				$head_of_department_id = isset($_POST['head_of_department']) && !empty($_POST['head_of_department']) && intval($_POST['head_of_department']) ? intval($_POST['head_of_department']) : null;
				$department_name = $_POST['department_name'];
				$jobs = json_decode($_POST['jobs']);

				if ($jobs === false) {
					return [false];
				}

				$HeadOfDepartment;
				if ($head_of_department_id !== null) {
					$HeadOfDepartment = \App\Models\Employee::where('id', $head_of_department_id)->first();
					if (!$HeadOfDepartment) {
						return [false, 'Head of department could not be found in the database'];
					}
				}

				$Department;
				if ($department_id == null) {
					if (DB::table('department')->where(['name' => $department_name])->count() > 0) {
						return [false, 'A department with this name already exists!'];
					}
					$Department = new \App\Models\Department();
				} else {
					$Department = \App\Models\Department::where('id', $department_id)->first();
					if (!$Department) {
						return [false, 'The department you were trying to edit could not be found in the database'];
					}
				}
				$Department->name = $department_name;
				$Department->head_of_department_id = $head_of_department_id;
				$Department->save();

				if ($department_id !== null) {
					foreach (\App\Models\Job::where('department_id', $department_id)->cursor() as $Job) {
						$found = false;
						foreach ($jobs as $job) {
							if ($job->new == true) {
								continue;
							}
							if ($job->id == $Job->id) {
								$found = true;
								break;
							}
						}
						if (!$found) {
							\App\Models\Employee::where('job_id', $Job->id)->update(['job_id' => null]);
							$Job->delete();
						}
					}
				}

				foreach ($jobs as $job) {
					if ($job->new === true) {
						if (count(DB::select('SELECT NULL FROM department_jobs WHERE `department_id` = ? AND LOWER(`title`) = ? LIMIT 1', [$Department->id, strtolower($job->name)])) === 0) {
							$Job = new \App\Models\Job();
							$Job->title = $job->name;
							$Job->department_id = $Department->id;
							$Job->save();
						} else {
							return [false, 'A job in this department is already called "'.$job->name.'"'];
						}
					} else {
						$Job = \App\Models\Job::where('id', $job->id)->first();
						if (!$Job) {
							return [false, 'A job that you were editing in this department could not be found in the database'];
						}
						if ($job->name !== $Job->title) {
							if (count(DB::select('SELECT NULL FROM department_jobs WHERE `department_id` = ? AND LOWER(`title`) = ? LIMIT 1', [$Department->id, strtolower($job->name)])) === 0) {
								$Job->title = $job->name;
								$Job->save();
							} else {
								return [false, 'A job in this department is already called "'.$job->name.'"'];
							}
						}
					}
				}

				return [true, $department_id == null ? $Department : null];
			})();

			$valid = $validation[0];

			if ($valid === true) {
				DB::commit();
				if ($validation[1] !== null) {
					return \App\Helpers::JSONResponse(['success' => true, 'department_id' => $validation[1]->id]);
				} else {
					return \App\Helpers::JSONResponse(['success' => true]);
				}
			} else {
				DB::rollback();
				if (!isset($validation[1])) {
					return abort(400);
				} else {
					return \App\Helpers::JSONResponse(['success' => false, 'error' => $validation[1]]);
				}
			}
		} else {
			if ($department_id != null) {
				$Department = \App\Models\Department::where('id', $department_id)->first();
				if (!$Department) {
					return abort(404);
				}

				return view('departments.edit', ['Department' => $Department]);
			} else {
				return view('departments.edit', ['Department' => new \App\Models\Department()]);
			}
		}
	}
}
