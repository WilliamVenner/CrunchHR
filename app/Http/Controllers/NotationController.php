<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotationController extends Controller
{
	public function Update(Request $request)
	{
		$UserEmployee = $request->get('UserEmployee');

		if (isset($_POST['new'])) {
			$Notation = new \App\Models\Notation();
			$Notation->author_id = $UserEmployee->id;
			$Notation->content = $_POST['content'];
			$Notation->employee_id = $_POST['employee_id'];
			$Notation->save();
		}
		if (isset($_POST['delete'])) {
			$Notation = \App\Models\Notation::where('id', $_POST['delete'])->first();
			if ($Notation) {
				$Notation->delete();
			}
		}

		return redirect($request->path());
	}

	public function View(Request $request, $employee_id)
	{
		$UserEmployee = $request->get('UserEmployee');

		$Employee = \App\Models\Employee::where('id', $employee_id)->first();
		if (!$Employee) {
			return abort(404, 'Employee does not exist');
		}

		$Notations = \App\Models\Notation::where('employee_id', $Employee->id)->orderBy('created_at', 'DESC')->get();

		return view('employees.notations', ['Employee' => $Employee, 'Notations' => $Notations, 'EmployeeURLID' => $employee_id]);
	}
}
