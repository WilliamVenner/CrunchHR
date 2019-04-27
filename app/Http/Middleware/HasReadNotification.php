<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class HasReadNotification
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure                 $next
	 *
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$UserEmployee = $request->get('UserEmployee');
		if ($UserEmployee) {
			DB::table('notifications')->where([
				'employee_id' => $UserEmployee->id,
				'seen'        => '0',
				'link'        => $request->getRequestUri(),
			])->update(['seen' => '1']);
		}

		return $next($request);
	}
}
