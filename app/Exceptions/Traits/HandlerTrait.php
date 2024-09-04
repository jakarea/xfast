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

use App\Helpers\DBTool;
use Illuminate\Http\Request;

trait HandlerTrait
{
	/**
	 * @param \Throwable $e
	 * @param \Illuminate\Http\Request $request
	 * @param string|null $message
	 * @param int|null $status
	 * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
	 */
	protected function responseCustomError(
		\Throwable $e,
		Request    $request,
		?string    $message = null,
		?int       $status = null
	): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
	{
		// Get status code
		$defaultStatus = (method_exists($e, 'getStatusCode')) ? $e->getStatusCode() : 500;
		$status = !empty($status) ? $status : $defaultStatus;
		$status = isValidHttpStatus($status) ? $status : 500;
		
		// Get error message
		$message = !empty($message) ? $message : $e->getMessage();
		$message = !empty($message) ? $message : getHttpErrorMessage($status);
		
		if (isFromApi($request) || isFromAjax($request)) {
			$data = [
				'success'   => false,
				'message'   => strip_tags($message),
				'exception' => $this,
			];
			
			return apiResponse()->json($data, $status);
		}
		
		$data = [
			'message'   => $message,
			'status'    => $status,
			'exception' => $this,
		];
		
		return response()->view('errors.custom', $data, $status);
	}
	
	/**
	 * Test Database Connection
	 *
	 * @return bool
	 * @throws \App\Exceptions\Custom\CustomException
	 */
	private function testDatabaseConnection(): bool
	{
		$pdo = DBTool::getPDOConnexion([], true);
		
		return ($pdo instanceof \PDO);
	}
	
	/**
	 * Create a config var for current language
	 *
	 * @return void
	 */
	private function getLanguage(): void
	{
		// Get the language only the app is already installed
		// to prevent HTTP 500 error through DB connexion during the installation process.
		if (appInstallFilesExist()) {
			// $this->app['config']->set('lang.code', config('app.locale'));
			$this->config->set('lang.code', config('app.locale'));
		}
	}
	
	/**
	 * Clear Laravel Log files
	 *
	 * @return void
	 */
	private function clearLog(): void
	{
		$mask = storage_path('logs') . DIRECTORY_SEPARATOR . '*.log';
		$logFiles = glob($mask);
		if (is_array($logFiles) && !empty($logFiles)) {
			foreach ($logFiles as $filename) {
				@unlink($filename);
			}
		}
	}
}
