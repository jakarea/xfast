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

namespace App\Exceptions\Traits;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

trait ExceptionTrait
{
	/**
	 * Is a PDO Exception
	 *
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isPDOException(\Throwable $e): bool
	{
		if (
			($e instanceof \PDOException)
			|| $e->getCode() == 1045
			|| str_contains($e->getMessage(), 'SQLSTATE')
			|| str_contains($e->getFile(), 'Database/Connectors/Connector.php')
		) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isQueryException(\Throwable $e): bool
	{
		return ($e instanceof QueryException);
	}
	
	/**
	 * Check if it is a DB connection exception
	 *
	 * DB Connection Error:
	 * http://dev.mysql.com/doc/refman/5.7/en/error-messages-server.html
	 *
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isDBConnectionException(\Throwable $e): bool
	{
		$dbErrorCodes = [
			'mysql'        => ['1042', '1044', '1045', '1046', '1049'],
			'standardized' => ['08S01', '42000', '28000', '3D000', '42000', '42S22'],
		];
		
		return (
			$this->isPDOException($e)
			|| in_array($e->getCode(), $dbErrorCodes['mysql'])
			|| in_array($e->getCode(), $dbErrorCodes['standardized'])
		);
	}
	
	/**
	 * Check if it is a DB table error exception
	 *
	 * DB Connection Error:
	 * http://dev.mysql.com/doc/refman/5.7/en/error-messages-server.html
	 *
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isDBTableException(\Throwable $e): bool
	{
		$tableErrorCodes = [
			'mysql'        => ['1051', '1109', '1146'],
			'standardized' => ['42S02'],
		];
		
		return (
			$this->isPDOException($e)
			|| in_array($e->getCode(), $tableErrorCodes['mysql'])
			|| in_array($e->getCode(), $tableErrorCodes['standardized'])
		);
	}
	
	/**
	 * @param \Throwable $e
	 * @return string
	 */
	protected function isDBTableExceptionMessage(\Throwable $e): string
	{
		$message = 'Some tables of the database are absent.' . "\n";
		$message .= $e->getMessage() . "\n";
		$message .= '1/ Remove all tables from the database (if existing)' . "\n";
		$message .= '2/ Delete the <code>/.env</code> file (required before re-installation)' . "\n";
		$message .= '3/ and reload this page -or- go to install URL: <a href="' . url('install') . '">' . url('install') . '</a>.' . "\n";
		$message .= 'BE CAREFUL: If your site is already in production, you will lose all your data in both cases.' . "\n";
		
		return $message;
	}
	
	/**
	 * Determine if the given exception is an HTTP exception.
	 *
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isHttpException(\Throwable $e): bool
	{
		return $e instanceof HttpExceptionInterface;
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isHttp404Exception(\Throwable $e): bool
	{
		return (
			$this->isHttpException($e)
			&& method_exists($e, 'getStatusCode')
			&& $e->getStatusCode() == 404
		);
	}
	
	/**
	 * Check if it is an HTTP Method Not Allowed exception
	 *
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isHttp405Exception(\Throwable $e): bool
	{
		return (
			$e instanceof MethodNotAllowedHttpException
			|| (
				$this->isHttpException($e)
				&& method_exists($e, 'getStatusCode')
				&& $e->getStatusCode() == 405
			)
		);
	}
	
	/**
	 * Check it is a 'Post Too Large' exception
	 *
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isHttp413Exception(\Throwable $e): bool
	{
		return (
			$e instanceof PostTooLargeException
			|| (
				$this->isHttpException($e)
				&& method_exists($e, 'getStatusCode')
				&& $e->getStatusCode() == 413
			));
	}
	
	/**
	 * Check if the page is expired
	 *
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isHttp419Exception(\Throwable $e): bool
	{
		return (
			$this->isHttpException($e)
			&& method_exists($e, 'getStatusCode')
			&& $e->getStatusCode() == 419
		);
	}
	
	/**
	 * Check it is a Validation exception
	 *
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isValidationException(\Throwable $e): bool
	{
		return ($e instanceof ValidationException);
	}
	
	/**
	 * Check if it is caching exception (APC or Redis)
	 *
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isCachingException(\Throwable $e): bool
	{
		return ($this->isAPCCachingException($e) || $this->isRedisCachingException($e));
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isAPCCachingException(\Throwable $e): bool
	{
		return (bool)preg_match('#apc_#ui', $e->getMessage());
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isRedisCachingException(\Throwable $e): bool
	{
		return (bool)preg_match('#/predis/#i', $e->getFile());
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isModelNotFoundException(\Throwable $e): bool
	{
		return ($e instanceof ModelNotFoundException);
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isTokenMismatchException(\Throwable $e): bool
	{
		return ($e instanceof TokenMismatchException);
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isAuthenticationException(\Throwable $e): bool
	{
		return ($e instanceof AuthenticationException);
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isThrottleRequestsException(\Throwable $e): bool
	{
		return ($e instanceof ThrottleRequestsException);
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isMaximumExecutionTimeException(\Throwable $e): bool
	{
		return (
			str_contains($e->getMessage(), 'Maximum execution time')
			&& str_contains($e->getMessage(), 'exceeded')
		);
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isFullMemoryException(\Throwable $e): bool
	{
		return (
			str_contains($e->getMessage(), 'Allowed memory size of')
			&& str_contains($e->getMessage(), 'tried to allocate')
		);
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isDBTooManyConnectionsException(\Throwable $e): bool
	{
		return (
			str_contains($e->getMessage(), 'max_user_connections')
			&& str_contains($e->getMessage(), 'active connections')
		);
	}
	
	/**
	 * @param \Throwable $e
	 * @return string
	 */
	protected function getMaximumExecutionTimeMessage(\Throwable $e): string
	{
		// Maximum execution time exceeded
		$message = $e->getMessage() . ". \n";
		$message .= 'The server\'s maximum execution time must be increased so that it can support the execution time of the request.';
		$message .= "\n\n";
		$message .= 'For quick fix to complete the execution of the current request, you can refresh this page as many times until this error disappears.
		If the error persists you must be increase your server\'s "max_execution_time" and "max_input_time" directives.';
		
		return $message;
	}
	
	/**
	 * @param \Throwable $e
	 * @return string
	 */
	protected function getFullMemoryMessage(\Throwable $e): string
	{
		// Memory is full
		$message = $e->getMessage() . ". \n";
		$message .= 'The server\'s memory must be increased so that it can support the load of the requested resource.';
		
		return $message;
	}
	
	/**
	 * @param \Throwable $e
	 * @return string
	 */
	protected function getTooManyConnectionsMessage(\Throwable $e): string
	{
		// Too many connections
		$message = 'We are currently receiving a large number of connections. ';
		$message .= 'Please try again later. We apologize for the inconvenience.';
		
		return $message;
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isExceptionFromPluginClassLoading(\Throwable $e): bool
	{
		// Check if there is no plugin class loading issue (inside composer class loader)
		return (
			method_exists($e, 'getFile') && method_exists($e, 'getMessage')
			&& !empty($e->getFile()) && !empty($e->getMessage())
			&& str_contains($e->getFile(), '/vendor/composer/ClassLoader.php')
			&& str_contains($e->getMessage(), '/extras/plugins/')
		);
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isCompatibilityExceptionFromPluginCode(\Throwable $e): bool
	{
		// Check if there are no problems in a plugin code
		return (
			method_exists($e, 'getFile') && method_exists($e, 'getMessage')
			&& !empty($e->getFile()) && !empty($e->getMessage())
			&& str_contains($e->getFile(), '/extras/plugins/')
			&& str_contains($e->getMessage(), 'extras\plugins\\')
			&& str_contains($e->getMessage(), 'must be compatible')
		);
	}
	
	/**
	 * @param \Throwable $e
	 * @return bool
	 */
	protected function isExceptionFileNotLocatedInVendor(\Throwable $e): bool
	{
		// Check if the error is not from the '/vendor/' folder
		return (
			method_exists($e, 'getFile')
			&& !empty($e->getFile())
			&& !str_contains($e->getFile(), '/vendor/')
		);
	}
}
