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

namespace App\Exceptions;

use App\Exceptions\Traits\ExceptionTrait;
use App\Exceptions\Traits\HandlerTrait;
use App\Exceptions\Traits\JsonRenderTrait;
use App\Exceptions\Traits\NotificationTrait;
use App\Exceptions\Traits\PluginTrait;
use App\Helpers\Cookie;
use App\Helpers\UrlGen;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Request;
use Prologue\Alerts\Facades\Alert;

class Handler
{
	use ExceptionTrait, HandlerTrait, JsonRenderTrait, PluginTrait, NotificationTrait;
	
	protected mixed $app;
	protected ConfigRepository $config;
	
	public function __construct()
	{
		$this->app = app();
		$this->config = $this->app->instance('config', new ConfigRepository());
		
		// Fix the 'files' & 'filesystem' binging.
		$this->app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
		
		// Create a config var for current language
		$this->getLanguage();
	}
	
	public function __invoke(Exceptions $exceptions): void
	{
		/*
		 * Report or log an exception
		 */
		$exceptions->report(function (\Throwable $e) {
			// Clear PDO error log during installation
			if (!appInstallFilesExist()) {
				if ($this->isPDOException($e)) {
					$this->clearLog();
					
					// Stop the propagation of the exception to the default logging stack
					return false;
				}
			}
			
			if (appInstallFilesExist()) {
				$this->sendNotification($e);
			}
		});
		
		/*
		 * Render an exception into an HTTP response
		 */
		$exceptions->render(function (\Throwable $e, Request $request) {
			// Restore the request headers back to the original state
			// saved before API call (using sub request option)
			if (config('request.original.headers')) {
				request()->headers->replace(config('request.original.headers'));
			}
			
			// API or AJAX requests exception
			if (isFromApi($request) || isFromAjax($request)) {
				return $this->jsonRender($e, $request);
			}
			
			// Maximum execution time exceeded exception
			if ($this->isMaximumExecutionTimeException($e)) {
				return $this->responseCustomError($e, $request, $this->getMaximumExecutionTimeMessage($e));
			}
			
			// Memory is full exception
			// Called only when reporting some Laravel error traces
			if ($this->isFullMemoryException($e)) {
				return $this->responseCustomError($e, $request, $this->getFullMemoryMessage($e));
			}
			
			// HTTP exception
			if ($this->isHttpException($e)) {
				// Check if the app is installed when page is not found (or when 404 page is called),
				// to prevent any DB error when the app is not installed yet
				if ($this->isHttp404Exception($e)) {
					if (!appIsInstalled()) {
						if ($request->input('exception') != '404') {
							return redirect()->to(getRawBaseUrl() . '/install?exception=404');
						}
					}
				}
				
				// HTTP Method Not Allowed Exception
				if ($this->isHttp405Exception($e)) {
					$message = "Whoops! Seems you use a bad request method. Please try again.";
					$backLink = ' <a href="' . url()->previous() . '">' . t('Back') . '</a>';
					$message = $message . $backLink;
					
					return $this->responseCustomError($e, $request, $message, 405);
				}
				
				// Post Too Large Exception
				if ($this->isHttp413Exception($e)) {
					$message = 'Maximum data (including files to upload) size to post and memory usage are limited on the server.';
					$message = 'Payload Too Large. ' . $message;
					$backLink = ' <a href="' . url()->previous() . '">' . t('Back') . '</a>';
					$message = $message . $backLink;
					
					return $this->responseCustomError($e, $request, $message, 413);
				}
				
				// Authentication Timeout Exception
				if ($this->isHttp419Exception($e)) {
					$message = t('page_expired');
					if (isAdminPanel()) {
						Alert::error($message)->flash();
					} else {
						flash($message)->error();
					}
					
					$previousUrl = url()->previous();
					if (!str_contains($previousUrl, 'AuthTimeout')) {
						$queryString = (parse_url($previousUrl, PHP_URL_QUERY) ? '&' : '?') . 'error=AuthTimeout';
						$previousUrl = $previousUrl . $queryString;
						return redirect()->to($previousUrl)->withInput();
					}
				}
				
				// EXIT
				return false;
			}
			
			// Token Mismatch Exception
			if ($this->isTokenMismatchException($e)) {
				$message = t('session_expired_reload_needed');
				if (isAdminPanel()) {
					Alert::error($message)->flash();
				} else {
					flash($message)->error();
				}
				
				$previousUrl = url()->previous();
				if (!str_contains($previousUrl, 'CsrfToken')) {
					$queryString = (parse_url($previousUrl, PHP_URL_QUERY) ? '&' : '?') . 'error=CsrfToken';
					$previousUrl = $previousUrl . $queryString;
					return redirect()->to($previousUrl)->withInput();
				}
			}
			
			// Validation Exception
			if ($this->isValidationException($e)) {
				/*
				 * Temporary fix when forms (after failed validation) are not redirect to back with explicit error messages per field
				 * Issue found on type of server: Apache/2.4.52 (Win64) OpenSSL/1.1.1m PHP/8.1.2
				 */
				if (method_exists($e, 'errors')) {
					return back()->withErrors($e->errors())->withInput();
				}
			}
			
			// Caching (APC or Redis) Exception
			if ($this->isCachingException($e)) {
				$message = $e->getMessage() . "\n";
				if ($this->isAPCCachingException($e)) {
					$message .= 'This looks like that the <a href="https://www.php.net/manual/en/book.apcu.php" target="_blank">APC extension</a> ';
					$message .= 'is not installed (or not properly installed) for PHP.' . "\n";
				}
				$message .= 'Make sure you have properly installed the components related to the selected cache driver on your server.' . "\n";
				$message .= 'To get your website up and running again you have to change the cache driver in the /.env file ';
				$message .= 'with the "file" or "array" driver (example: CACHE_STORE=file).' . "\n";
				
				return $this->responseCustomError($e, $request, $message);
			}
			
			// PDO & DB Exception
			if ($this->isPDOException($e)) {
				// Check if the app installation files exist,
				// to prevent any DB error (from the Admin Panel) when the app is not installed yet.
				if (!appInstallFilesExist()) {
					if ($request->input('exception') != 'PDO') {
						$msg = $e->getMessage();
						if (!empty($msg)) {
							return $this->responseCustomError($e, $request, $msg);
						}
						
						$this->clearLog();
						
						return redirect()->to(getRawBaseUrl() . '/install?exception=PDO');
					}
				}
				
				if (appInstallFilesExist()) {
					// Too Many Connections Exception
					if ($this->isDBTooManyConnectionsException($e)) {
						return $this->responseCustomError($e, $request, $this->getTooManyConnectionsMessage($e));
					}
					
					if ($this->testDatabaseConnection() !== true) {
						$msg = 'Connection to the database failed.';
						
						return $this->responseCustomError($e, $request, $msg);
					}
				}
				
				// DB Errors Exception
				if ($this->isDBConnectionException($e)) {
					return $this->responseCustomError($e, $request);
				}
				
				// DB Tables & Columns Errors Exception
				if ($this->isDBTableException($e)) {
					return $this->responseCustomError($e, $request, $this->isDBTableExceptionMessage($e));
				}
			}
			
			// Convert an authentication exception into an unauthenticated response
			if ($this->isAuthenticationException($e)) {
				$message = 'Unauthenticated or Token Expired, Please Login.';
				if (isAdminPanel()) {
					Alert::error($message)->flash();
				} else {
					flash($message)->error();
				}
				
				return redirect()->guest(UrlGen::loginPath());
			}
			
			// Try to fix the cookies issue related the Laravel security release:
			// https://laravel.com/docs/5.6/upgrade#upgrade-5.6.30
			if (
				str_contains($e->getMessage(), 'unserialize()')
				&& request()->query('exception') != 'unserialize'
			) {
				// Unset cookies
				Cookie::forgetAll();
				
				// Customize and Redirect to the previous URL
				$previousUrl = url()->previous();
				$queryString = (parse_url($previousUrl, PHP_URL_QUERY) ? '&' : '?') . 'exception=unserialize';
				$previousUrl = $previousUrl . $queryString;
				
				redirectUrl($previousUrl, 301, config('larapen.core.noCacheHeaders'));
			}
			
			// Check if there is no plugin class loading issue (inside composer class loader)
			if ($this->isExceptionFromPluginClassLoading($e)) {
				$message = $e->getMessage();
				$message = !empty($message) ? $this->tryToFixPluginDirName($message) : null;
				
				return $this->responseCustomError($e, $request, $message);
			}
			
			// Check if there are no problems in a plugin code
			if ($this->isCompatibilityExceptionFromPluginCode($e)) {
				$message = $e->getMessage();
				$message = !empty($message) ? $this->tryToArchivePlugin($message) : null;
				
				return $this->responseCustomError($e, $request, $message);
			}
			
			// Show custom error 500 page,
			// when the error is not from the '/vendor/' folder
			if ($this->isExceptionFileNotLocatedInVendor($e)) {
				return response()->view('errors.500', ['exception' => $e], 500);
			}
		});
	}
}
