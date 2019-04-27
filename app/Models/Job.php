<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
	public $timestamps = false;
	protected $table = 'department_jobs';

	public function department()
	{
		return $this->hasOne(\App\Models\Department::class, 'id', 'department_id');
	}
}
