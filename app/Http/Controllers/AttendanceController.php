<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
	public function DisplayAttendance(Request $request)
	{
		$UserEmployee = $request->get('UserEmployee');

		return view('employees.clock', ['TodaysAttendance' => \App\Models\Attendance::where('employee_id', $UserEmployee->id)->whereRaw('`date` = DATE(NOW())')->first()]);
	}

	public function UpdateAttendance(Request $request)
	{
		$UserEmployee = $request->get('UserEmployee');
		$TodaysAttendance = \App\Models\Attendance::where('employee_id', $UserEmployee->id)->whereRaw('`date` = DATE(NOW())')->first();

		if ($TodaysAttendance === null && isset($_POST['clock-in'])) {
			$TodaysAttendance = new \App\Models\Attendance();
			$TodaysAttendance->employee_id = $UserEmployee->id;
			$TodaysAttendance->date = DB::raw('CURRENT_DATE');
		}

		if (isset($_POST['clock-in'])) {
			$TodaysAttendance->clocked_in = DB::raw('CURRENT_TIME');
		} elseif (isset($_POST['clock-out'])) {
			if (!$TodaysAttendance) {
				return abort(400);
			}
			$TodaysAttendance->clocked_out = DB::raw('CURRENT_TIME');
		} else {
			return abort(400);
		}

		$TodaysAttendance->save();

		return redirect('/'.$request->path());
	}

	public function ViewCalendar()
	{
		$month = intval(date('n'));
		$year = intval(date('Y'));

		if (isset($_POST['year-month'])) {
			$split = explode('-', $_POST['year-month']);
			$year = intval($split[0]);
			$month = intval($split[1]);
			if ($year === 0) {
				$year = intval(date('Y'));
			}
			if ($month === 0) {
				$month = intval(date('Y'));
			}
		}
		
		$department_count = \App\Models\Department::count();
		
		$department_attendance_data = DB::select('
			SELECT DAY(`date`) AS `day`, a.`department_id`, COUNT(DISTINCT attendance.`employee_id`) / (SELECT COUNT(*) FROM employees WHERE `job_id` IN (SELECT `id` FROM department_jobs WHERE a.`department_id`=`department_id`)) * 100 AS `attendance_pct` FROM `attendance`
			INNER JOIN employees ON employees.`id`=attendance.`employee_id`
			INNER JOIN department_jobs a ON employees.`job_id`=a.`id`
			WHERE `clocked_in` IS NOT NULL AND MONTH(`date`) = ? AND YEAR(`date`) = ?
			GROUP BY `date`, `department_id`
			ORDER BY `date` DESC, `attendance_pct` DESC
		', [$month, $year]);
		
		$department_attendance = [];
		foreach($department_attendance_data as $row) {
			if (!isset($department_attendance[intval($row -> day)])) {
				$department_attendance[intval($row -> day)] = [];
			}
			$department_attendance[intval($row -> day)][] = $row;
		}

		return view('attendance.calendar', ['month' => $month, 'year' => $year, 'department_attendance' => $department_attendance, 'department_count' => $department_count]);
	}
}
