<?php

namespace App;

use Illuminate\Support\Facades\DB;

class Notifications
{
	public static function NotifyAll($title, $content, $link = null, $exclude = null)
	{
		$Employees;
		if ($exclude === null) {
			$Employees = \App\Models\Employee::all();
		} else {
			$Employees = \App\Models\Employee::where('id', '<>', $exclude)->get();
		}

		foreach ($Employees as $Employee) {
			$Employee->SendNotification($title, $content, $link);
		}
	}

	public static function NotifyManagement($title, $content, $link = null)
	{
		$Employees = \App\Models\Employee::hydrate(DB::select('SELECT `id` FROM `employees` WHERE `job_id` IN (SELECT `id` FROM `department_jobs` WHERE `department_id` IN (SELECT `id` FROM `department` WHERE `management`=1))'));

		foreach ($Employees as $Employee) {
			$Employee->SendNotification($title, $content, $link);
		}
	}

	public static function NotifyHumanResources($title, $content, $link = null)
	{
		$Employees = \App\Models\Employee::hydrate(DB::select('SELECT `id` FROM `employees` WHERE `job_id` IN (SELECT `id` FROM `department_jobs` WHERE `department_id` IN (SELECT `id` FROM `department` WHERE `human_resources`=1))'));

		foreach ($Employees as $Employee) {
			$Employee->SendNotification($title, $content, $link);
		}
	}

	public static function NotifyEmployee($employee_id, $title, $content, $link = null)
	{
		$Employee = \App\Models\Employee::where('id', $employee_id)->first();
		$Employee->SendNotification($title, $content, $link);
	}
}
