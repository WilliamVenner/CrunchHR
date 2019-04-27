<?php

namespace App\Models;

use App\Jobs\SendPushNotification;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
	const UPDATED_AT = null;
	protected $table = 'notifications';

	public static function boot()
	{
		parent::boot();

		self::created(function ($Notification) {
			if (\App\Models\PushNotificationSubscription::where('employee_id', $Notification->employee->id)->count() === 0) {
				return;
			}

			SendPushNotification::dispatch($Notification);
		});
	}

	public function employee()
	{
		return $this->hasOne(\App\Models\Employee::class, 'id', 'employee_id');
	}
}
