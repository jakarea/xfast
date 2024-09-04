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

namespace App\Exceptions\Custom;

use App\Exceptions\Traits\ExceptionTrait;
use App\Exceptions\Traits\HandlerTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CustomException extends Exception
{
	use ExceptionTrait, HandlerTrait;
	
	/**
	 * Report the exception.
	 */
	public function report(): void
	{
		if (appInstallFilesExist()) {
			Log::error($this->getMessage());
		} else {
			// Clear PDO error log during installation
			if ($this->isPDOException($this)) {
				$this->clearLog();
			}
		}
	}
	
	/**
	 * Render the exception into an HTTP response.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
	 */
	public function render(Request $request): Response|\Illuminate\Http\JsonResponse
	{
		$message = $this->getMessage();
		
		// Explode the message by new line
		$lines = preg_split('/\r\n|\r|\n/', $message);
		$countLines = is_array($lines) ? count($lines) : 0;
		if ($countLines > 0 && $countLines <= 3) {
			$message = '<div class="align-center text-danger">' . $message . '</div>';
		}
		
		return $this->responseCustomError($this, $request, $message);
	}
}
