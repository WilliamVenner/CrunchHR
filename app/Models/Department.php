<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
	public $timestamps = false;
	protected $table = 'department';

	public function jobs()
	{
		return $this->hasMany(\App\Models\Job::class, 'department_id', 'id');
	}

	public function head_of_department()
	{
		return $this->hasOne(\App\Models\Employee::class, 'id', 'head_of_department_id');
	}
}
