<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
	public function ViewAll(Request $request)
	{
		$UserEmployee = $request->get('UserEmployee');

		$notifications = \App\Models\Notification::where('employee_id', $UserEmployee->id)->orderBy('created_at', 'DESC')->get();

		return view('notifications.view', ['notifications' => $notifications]);
	}

	public function OpenNotification(Request $request, $id)
	{
		$UserEmployee = $request->get('UserEmployee');
		if (!is_numeric($id)) {
			return abort(400);
		}

		$Notification = \App\Models\Notification::where(['id' => $id, 'employee_id' => $UserEmployee->id])->first();
		if (!$Notification) {
			return abort(404);
		}

		$Notification->seen = 1;
		$Notification->save();

		return redirect($Notification->link);
	}

	public function DeleteNotification(Request $request, $id)
	{
		if (!is_numeric($id)) {
			return abort(400);
		}

		$UserEmployee = $request->get('UserEmployee');
		$Notification = \App\Models\Notification::where(['id' => $id, 'employee_id' => $UserEmployee->id])->first();
		if (!$Notification) {
			return abort(404);
		}

		$Notification->delete();
	}

	public function DeleteAllNotifications(Request $request)
	{
		$UserEmployee = $request->get('UserEmployee');
		\App\Models\Notification::where(['employee_id' => $UserEmployee->id, 'viewed' => '1'])->delete();
	}

	public function Subscribe(Request $request)
	{
		if (!isset($_POST['subscription_data'])) {
			return abort(400);
		}

		$UserEmployee = $request->get('UserEmployee');

		$subscription_data = json_decode($_POST['subscription_data']);
		if (!$subscription_data || !isset($subscription_data->endpoint) || !isset($subscription_data->contentEncoding)) {
			return abort(400);
		}

		DB::insert('INSERT INTO notification_subscriptions (`employee_id`, `endpoint`, `data`) VALUES(?,?,?)', [$UserEmployee->id, $subscription_data->endpoint, $_POST['subscription_data']]);
	}

	public function Unsubscribe(Request $request)
	{
		if (!isset($_POST['endpoint'])) {
			return abort(400);
		}

		$UserEmployee = $request->get('UserEmployee');
		DB::delete('DELETE FROM notification_subscriptions WHERE `employee_id`=? AND `endpoint`=?', [$UserEmployee->id, $_POST['endpoint']]);
	}
}
