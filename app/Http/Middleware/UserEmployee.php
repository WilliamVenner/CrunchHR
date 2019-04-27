<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class UserEmployee
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
		$UserEmployee = \App\Models\Employee::where('email', Auth::user()->email)->first();
		view()->share('UserEmployee', $UserEmployee);
		$request->attributes->set('UserEmployee', $UserEmployee);

		return $next($request);
	}
}
