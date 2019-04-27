<?php

namespace App\Http\Middleware;

use Closure;

class HumanResourcesOnly
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
		if (!$UserEmployee->is_human_resources()) {
			return abort(403, 'You must be part of human resources/management to view this page.');
		}

		return $next($request);
	}
}
