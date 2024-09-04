<?php
/*
 * LaraClassifier - Classified Ads Web Application
 * Copyright (c) BeDigit. All Rights Reserved
 *
 * Website: https://laraclassifier.com
 * Author: BeDigit | https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - https://codecanyon.net/licenses/standard
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated as Middleware;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated extends Middleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
	 * @param string ...$guards
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function handle(Request $request, Closure $next, string ...$guards): Response
	{
		$guards = empty($guards) ? [null] : $guards;
		
		foreach ($guards as $guard) {
			if (auth()->guard($guard)->check()) {
				if (isFromApi()) {
					return $next($request);
				}
				
				$url = isFromAdminPanel() ? admin_uri() : '/';
				$url .= '?login=success';
				
				return redirect()->to($url);
			}
		}
		
		return $next($request);
	}
}
