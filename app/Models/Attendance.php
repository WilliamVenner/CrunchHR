<?php

namespace App\Models;

use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
	use HasCompositePrimaryKey;

	protected $primaryKey = ['employee_id', 'date'];
	public $timestamps = false;
	protected $table = 'attendance';
}
