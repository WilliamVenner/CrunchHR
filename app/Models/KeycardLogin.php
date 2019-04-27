<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeycardLogin extends Model
{
	public $timestamps = false;
	protected $table = 'keycard_logins';
	protected $primaryKey = 'employee_id';
}
