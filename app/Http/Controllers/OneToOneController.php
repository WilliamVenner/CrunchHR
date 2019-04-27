<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OneToOneController extends Controller
{
	private static function GetOneToOnes($employee_id = null, $viewing_employee_id = null, $page = 1, $where = '')
	{
		if ($employee_id !== null) {
			return \App\Models\OneToOne::hydrate(DB::select('
				SELECT
					one_to_ones.`id`,
					one_to_ones.`employee_id`,
					one_to_ones.`subject`,
					one_to_ones.`created_at`,
					one_to_ones.`due`,
					IFNULL(one_to_ones_replies.`created_at`, one_to_ones.`created_at`) AS `last_updated`,
					`priority`,
					`locked`,
					one_to_ones_read_registry.`employee_id` IS NULL AS `unread`,
					(SELECT COUNT(*) FROM one_to_ones_replies WHERE one_to_ones_replies.`one_to_one_id` = one_to_ones.`id`) AS `messages`
				FROM `one_to_ones`
				LEFT JOIN one_to_ones_replies ON one_to_ones_replies.`one_to_one_id` = one_to_ones.`id`
				LEFT JOIN one_to_ones_read_registry ON one_to_ones_read_registry.`one_to_one_id` = one_to_ones.`id` AND one_to_ones_read_registry.`employee_id` = ?
				WHERE one_to_ones.`employee_id` = ?
				GROUP BY one_to_ones.`id`
				ORDER BY `locked` ASC, one_to_ones.`due` ASC, `priority` DESC, `unread` DESC
			', [$viewing_employee_id, $employee_id]));
		} else {
			return \App\Models\OneToOne::hydrate(DB::select('
				SELECT
					one_to_ones.`id`,
					one_to_ones.`employee_id`,
					one_to_ones.`subject`,
					one_to_ones.`created_at`,
					one_to_ones.`due`,
					IFNULL(one_to_ones_replies.`created_at`, one_to_ones.`created_at`) AS `last_updated`,
					`priority`,
					`locked`,
					one_to_ones_read_registry.`employee_id` IS NULL AS `unread`,
					(SELECT COUNT(*) FROM one_to_ones_replies WHERE one_to_ones_replies.`one_to_one_id` = one_to_ones.`id`) AS `messages`
				FROM `one_to_ones`
				LEFT JOIN one_to_ones_replies ON one_to_ones_replies.`one_to_one_id` = one_to_ones.`id`
				LEFT JOIN one_to_ones_read_registry ON one_to_ones_read_registry.`one_to_one_id` = one_to_ones.`id` AND one_to_ones_read_registry.`employee_id` = ?
				'.$where.'
				GROUP BY one_to_ones.`id`
				ORDER BY `locked` ASC, one_to_ones.`due` ASC, `priority` DESC, `unread` DESC
				LIMIT '.($page - 1) * 15 .',15
			', [$viewing_employee_id]));
		}
	}

	public function ViewAll(Request $request)
	{
		$UserEmployee = $request->get('UserEmployee');

		$where = '';

		if ((isset($_POST['hidelocked']) && $_POST['hidelocked'] == 'on') || (isset($_POST['hideread']) && $_POST['hideread'] == 'on')) {
			$where = 'WHERE ';
		}
		if (isset($_POST['hidelocked']) && $_POST['hidelocked'] == 'on') {
			$where .= '`locked`=0 AND ';
		}
		if (isset($_POST['hideread']) && $_POST['hideread'] == 'on') {
			$where .= 'one_to_ones_read_registry.`employee_id` IS NULL AND ';
		}
		if ($where !== '') {
			$where = preg_replace('/ AND $/', '', $where);
		}

		$pages = DB::select('
			SELECT CEIL(COUNT(*) / 15) AS count FROM one_to_ones
			LEFT JOIN one_to_ones_read_registry ON one_to_ones_read_registry.`one_to_one_id` = one_to_ones.`id` AND one_to_ones_read_registry.`employee_id` = ?
			'.$where.'
		', [$UserEmployee->id])[0]->count;

		$OneToOnes = OneToOneController::GetOneToOnes(null, $UserEmployee->id, \App\Pagination::Page($pages), $where);

		return view('1to1.list', ['OneToOnes' => $OneToOnes, 'pages' => $pages]);
	}

	public function ViewEmployee(Request $request, $employee_id = null)
	{
		$UserEmployee = $request->get('UserEmployee');

		$Employee = $employee_id === null ? $UserEmployee : \App\Models\Employee::where('id', $employee_id)->first();
		if (!$Employee) {
			return abort(404, 'Employee does not exist');
		}
		$OneToOnes = OneToOneController::GetOneToOnes($Employee->id, $UserEmployee->id);

		return view('employees.1to1s', ['OneToOnes' => $OneToOnes, 'Employee' => $Employee, 'EmployeeURLID' => $employee_id === null ? 'me' : $employee_id]);
	}

	public function New(Request $request)
	{
		if (isset($_POST['submit'])) {
			if (!isset($_POST['subject'], $_POST['employee_id'], $_POST['priority'], $_POST['due']) || empty($_POST['subject'])) {
				return abort(400);
			}
			$Employee = \App\Models\Employee::where('id', $_POST['employee_id'])->first();
			if (!$Employee) {
				return abort(400);
			}

			try {
				$due = new Carbon($_POST['due']);
			} catch (\Carbon\InvalidDateException $e) {
				return abort(400);
			}
			$due->endOfDay();

			$OneToOne = new \App\Models\OneToOne();
			$OneToOne->subject = $_POST['subject'];
			$OneToOne->employee_id = $Employee->id;
			$OneToOne->priority = min(max(intval($_POST['priority']), 0), 3);
			$OneToOne->due = $due;
			$OneToOne->save();

			$Employee->SendNotification('Human resources has started a new one-to-one with you', 'Please respond to the one-to-one and communicate with human resources', '/1to1/'.$OneToOne->id);

			return redirect('/1to1/'.$OneToOne->id);
		} else {
			return view('1to1.new');
		}
	}

	public function Update(Request $request, $_1to1_id)
	{
		$UserEmployee = $request->get('UserEmployee');

		$OneToOne = \App\Models\OneToOne::where('id', $_1to1_id)->first();
		if (!$OneToOne) {
			return abort(404);
		}

		if (isset($_POST['new'])) {
			$OneToOneReply = new \App\Models\OneToOneReply();
			$OneToOneReply->one_to_one_id = $OneToOne->id;
			$OneToOneReply->employee_id = $UserEmployee->id;
			$OneToOneReply->content = $_POST['content'];
			$OneToOneReply->save();

			if ($UserEmployee->is_human_resources()) {
				\App\Notifications::NotifyEmployee($OneToOne->employee_id, 'Human resources has replied to your one-to-one', 'Please review the one-to-one', '/1to1/'.$OneToOne->id);
			} else {
				\App\Notifications::NotifyHumanResources('An employee has responded to their one-to-one', 'Please review the one-to-one', '/1to1/'.$OneToOne->id);
			}

			return redirect($request->path());
		}

		if ($UserEmployee->is_human_resources()) {
			if (isset($_POST['lock'])) {
				$OneToOne->locked = 1;
				$OneToOne->save();

				\App\Notifications::NotifyEmployee($OneToOne->employee_id, 'Human resources has locked your one-to-one', 'Please review the one-to-one', '/1to1/'.$OneToOne->id);
			}
			if (isset($_POST['unlock'])) {
				$OneToOne->locked = 0;
				$OneToOne->save();

				\App\Notifications::NotifyEmployee($OneToOne->employee_id, 'Human resources has unlocked your one-to-one', 'Please review the one-to-one', '/1to1/'.$OneToOne->id);
			}
		}

		return redirect($request->path());
	}

	public function View(Request $request, $_1to1_id)
	{
		$UserEmployee = $request->get('UserEmployee');
		$OneToOne = \App\Models\OneToOne::hydrate(DB::select('
			SELECT
				one_to_ones.`id`,
				one_to_ones.`employee_id`,
				one_to_ones.`subject`,
				one_to_ones.`created_at`,
				one_to_ones.`due`,
				IFNULL(one_to_ones_replies.`created_at`, one_to_ones.`created_at`) AS `last_updated`,
				`priority`,
				`locked`,
				one_to_ones_read_registry.`employee_id` IS NULL AS `unread`,
				(SELECT COUNT(*) FROM one_to_ones_replies WHERE one_to_ones_replies.`one_to_one_id` = one_to_ones.`id`) AS `messages`
			FROM `one_to_ones`
			LEFT JOIN one_to_ones_replies ON one_to_ones_replies.`one_to_one_id` = one_to_ones.`id`
			LEFT JOIN one_to_ones_read_registry ON one_to_ones_read_registry.`one_to_one_id` = one_to_ones.`id` AND one_to_ones_read_registry.`employee_id` = ?
			WHERE one_to_ones.`id` = ?
		', [$UserEmployee->id, $_1to1_id]))->first();
		if (!$OneToOne) {
			return abort(404, 'One to one does not exist');
		}

		if (!$UserEmployee->is_human_resources() && $OneToOne->employee_id !== $UserEmployee->id) {
			return abort(403);
		}

		DB::insert('INSERT IGNORE INTO one_to_ones_read_registry (`one_to_one_id`, `employee_id`) VALUES(?,?)', [$OneToOne->id, $UserEmployee->id]);

		$OneToOneReplies = \App\Models\OneToOneReply::where('one_to_one_id', $OneToOne->id)->orderBy('created_at', 'DESC')->get();

		return view('1to1.view', ['OneToOne' => $OneToOne, 'OneToOneReplies' => $OneToOneReplies]);
	}
}
