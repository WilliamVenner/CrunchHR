<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
	public $timestamps = false;
	protected $table = 'employees';

	public function SendNotification($title, $content, $link = null)
	{
		$Notification = new \App\Models\Notification();
		$Notification->employee_id = $this->id;
		$Notification->title = $title;
		$Notification->content = $content;
		if ($link != null) {
			$Notification->link = $link;
		}
		$Notification->save();
	}

	private $profile_picture;

	public function picture($png = false)
	{
		if (!isset($this->profile_picture)) {
			$this->profile_picture = \App\Models\ProfilePicture::where('employee_id', $this->id)->first();
		}
		if ($this->profile_picture) {
			return '/assets/img/employees/'.$this->profile_picture->hash.($png === true ? '.png' : '.jpg');
		} elseif (isset($this->email) && $this->email != null && !empty($this->email)) {
			return 'https://www.gravatar.com/avatar/'.md5(strtolower(trim($this->email)));
		} else {
			return '/assets/img/employee.jpg';
		}
	}

	public function picture_hash()
	{
		if (!isset($this->profile_picture)) {
			$this->profile_picture = \App\Models\ProfilePicture::where('employee_id', $this->id)->first();
		}
		if ($this->profile_picture) {
			return $this->profile_picture->hash;
		} else {
			return null;
		}
	}

	public function full_name()
	{
		if ($this->middle_name) {
			return $this->first_name.' '.$this->middle_name.' '.$this->last_name;
		} else {
			return $this->first_name.' '.$this->last_name;
		}
	}

	public function job()
	{
		return $this->hasOne(\App\Models\Job::class, 'id', 'job_id');
	}

	public function is_management()
	{
		return $this->job && $this->job->department->management == 1;
	}

	public function is_human_resources()
	{
		return $this->job && $this->job->department->human_resources == 1 || $this->is_management();
	}
}
