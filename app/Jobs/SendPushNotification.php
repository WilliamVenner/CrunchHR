<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class SendPushNotification implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	public $tries = 1;
	public $timeout = 60;

	protected $Notification;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(Notification $Notification)
	{
		$this->Notification = $Notification;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$Employee = $this->Notification->employee;
		$PushNotificationSubscriptions = \App\Models\PushNotificationSubscription::where('employee_id', $Employee->id)->get();

		$icon = config('notifications.NOTIFICATION_ICON');

		$NotificationsStack = new \App\Structures\Stack();
		foreach ($PushNotificationSubscriptions as $PushNotificationSubscription) {
			$NotificationsStack->Push((object) [
				'subscription' => Subscription::create(json_decode($PushNotificationSubscription->data, true)),
				'payload'      => json_encode([
					'title' => $this->Notification->title,
					'body'  => $this->Notification->content,
					'icon'  => $icon,
					'badge' => $icon,
					'tag'   => $this->Notification->id,
					'data'  => $this->Notification->link,
				]),
			]);
		}

		if (!$NotificationsStack->IsEmpty()) {
			$webPush = new WebPush([
				'VAPID' => [
					'subject'    => 'mailto:william@venner.io',
					'publicKey'  => config('notifications.NOTIFICATION_PUBLIC_KEY'),
					'privateKey' => config('notifications.NOTIFICATION_PRIVATE_KEY'),
				],
			]);

			while (!$NotificationsStack->IsEmpty()) {
				$notification = $NotificationsStack->Pop();
				$webPush->sendNotification($notification->subscription, $notification->payload);
			}

			foreach ($webPush->flush() as $report) {
				if (!$report->isSuccess()) {
					error_log('[✖] Notification failed to dispatch: '.$report->getReason());
				}
			}
		}
	}
}
