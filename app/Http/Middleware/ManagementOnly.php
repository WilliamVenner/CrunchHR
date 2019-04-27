<?php

namespace App\Http\Middleware;

use Closure;

class ManagementOnly
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
		if (!$UserEmployee->is_management()) {
			return abort(403, 'You must be part of management to view this page.');
		}

		return $next($request);
	}
}
