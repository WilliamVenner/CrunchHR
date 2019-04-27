<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
	public function RequestLeave(Request $request)
	{
		$UserEmployee = $request->get('UserEmployee');
		if (isset($_POST['request-leave'])) {
			if (!isset($_POST['reason'], $_POST['from'], $_POST['to'])) {
				return abort(400);
			}
			if (!is_numeric($_POST['reason']) || !\App\Enums\LeaveReason::IsValidReason(intval($_POST['reason']))) {
				return abort(400);
			}

			$absent_from = new Carbon($_POST['from']);
			$absent_to = new Carbon($_POST['to']);
			if (!$absent_from || !$absent_to || $absent_from->greaterThan($absent_to)) {
				return abort(400);
			}

			$Leave = new \App\Models\Leave();
			$Leave->reason = $_POST['reason'];
			$Leave->notes = isset($_POST['notes']) && !empty($_POST['notes']) ? $_POST['notes'] : null;
			$Leave->absent_from = $absent_from;
			$Leave->absent_to = $absent_to;
			$Leave->employee_id = $UserEmployee->id;
			$Leave->save();

			\App\Notifications::NotifyHumanResources('An employee has requested leave', 'Please approve or reject their leave', '/hr/leave');

			return redirect('/employees/me/leave/history');
		} else {
			return view('employees.leave.request', ['EmployeeURLID' => 'me']);
		}
	}

	public function ViewLeaveRequests()
	{
		$Leaves = \App\Models\Leave::where('approved', '0')->orderBy('requested', 'ASC')->get();

		return view('employees.leave.allrequests', ['Leaves' => $Leaves]);
	}

	public function ViewHistory(Request $request, $employee_id = 'me')
	{
		$Employee = $employee_id === 'me' ? $request->get('UserEmployee') : \App\Models\Employee::where('id', $employee_id)->first();
		if (!$Employee) {
			return abort(404, 'Employee not found');
		}
		$Leaves = \App\Models\Leave::where('employee_id', $Employee->id)->orderBy('requested', 'DESC')->get();

		return view('employees.leave.history', ['Leaves' => $Leaves, 'EmployeeURLID' => $employee_id, 'Employee' => $Employee]);
	}

	public function ViewCalendar(Request $request, $employee_id = 'me')
	{
		$Employee = $employee_id === 'me' ? $request->get('UserEmployee') : \App\Models\Employee::where('id', $employee_id)->first();
		if (!$Employee) {
			return abort(404, 'Employee not found');
		}

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

		$padded_month = str_pad($month, 2, '0', STR_PAD_LEFT);
		$padded_last_day_of_month = str_pad(cal_days_in_month(CAL_GREGORIAN, $month, $year), 2, '0', STR_PAD_LEFT);

		$absent_days = [];
		for ($i = 1; $i <= cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++) {
			$absent_days[$i] = false;
		}

		$calendar_data = DB::select("
			SELECT
				DAY(`absent_from`) AS `from_day`,
				DAY(`absent_to`) AS `to_day`,
				`reason`,
				`notes`
			FROM `leaves`
			WHERE
				`employee_id`=? AND
				`approved`=1 AND
				`absent_from` >= '$year-$padded_month-01' AND
				`absent_from` <= '$year-$padded_month-$padded_last_day_of_month'
			ORDER BY `from_day` ASC
		", [$Employee->id]);
		foreach ($calendar_data as $row) {
			for ($i = $row->from_day; $i <= $row->to_day; $i++) {
				$absent_days[$i] = true;
			}
		}

		return view('employees.leave.calendar', ['month' => $month, 'year' => $year, 'absent_days' => $absent_days, 'EmployeeURLID' => $employee_id, 'Employee' => $Employee]);
	}

	public function UpdateApproval()
	{
		if (!isset($_POST['id'], $_POST['approved']) || ($_POST['approved'] != '1' && $_POST['approved'] != '0')) {
			return abort(400);
		}

		$Leave = \App\Models\Leave::where('id', $_POST['id'])->first();
		if (!$Leave) {
			return abort(404);
		}

		$Leave->approved = $_POST['approved'] == '1' ? 1 : 2;
		$Leave->save();

		$Employee = \App\Models\Employee::where('id', $Leave->employee_id)->first();

		if ($_POST['approved'] == '1') {
			$Employee->SendNotification('Your leave request has been approved', 'Your recent leave request has been approved by human resources', '/employees/me/leave/history');
		} else {
			$Employee->SendNotification('Your leave request has been rejected', 'Your recent leave request has been rejected by human resources', '/employees/me/leave/history');
		}
	}
}
