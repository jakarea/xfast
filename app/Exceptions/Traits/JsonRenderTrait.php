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

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

trait JsonRenderTrait
{
	/**
	 * @param \Throwable $e
	 * @param \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function jsonRender(Throwable $e, Request $request): \Illuminate\Http\JsonResponse
	{
		// Memory Is Full Exception
		// Called only when reporting some Laravel error traces
		if ($this->isFullMemoryException($e)) {
			$data = [
				'success' => false,
				'message' => strip_tags($this->getFullMemoryMessage($e)),
			];
			
			return apiResponse()->json($data, 500);
		}
		
		// HTTP Exception
		if ($this->isHttpException($e)) {
			if ($this->isHttp404Exception($e)) {
				$msg = !empty($e->getMessage()) ? $e->getMessage() : 'Page not found.';
				
				$data = [
					'success' => false,
					'message' => $msg,
				];
				
				return apiResponse()->json($data, 404);
			}
			
			// Post Too Large Exception
			if ($this->isHttp413Exception($e)) {
				$message = 'Maximum data (including files to upload) size to post and memory usage are limited on the server.';
				$data = [
					'success' => false,
					'message' => $message,
					'code'    => $e->getCode(),
				];
				
				if (doesRequestIsFromWebApp($request) || isFromAjax($request)) {
					$data['error'] = $message; // for bootstrap-fileinput
				}
				
				return apiResponse()->json($data, Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
			}
			
			// Authentication Timeout Exception
			if ($this->isHttp419Exception($e)) {
				$message = t('page_expired_reload_needed');
				$data = [
					'success' => false,
					'message' => $message,
				];
				
				if (doesRequestIsFromWebApp($request) || isFromAjax($request)) {
					$data['error'] = $message; // for bootstrap-fileinput
				}
				
				return apiResponse()->json($data, 419);
			}
		}
		
		// Model Not Found Exception
		if ($this->isModelNotFoundException($e)) {
			$data = [
				'success' => false,
				'message' => 'Entry for ' . str_replace('App\\', '', $e->getModel()) . ' not found.',
			];
			
			return apiResponse()->json($data, 404);
		}
		
		// DB Query Exception
		if ($this->isQueryException($e)) {
			$data = [
				'success'   => false,
				'message'   => 'There was issue with the query.',
				'exception' => $e,
			];
			
			return apiResponse()->json($data, 500);
		}
		
		// Convert an authentication exception into an unauthenticated response
		if ($this->isAuthenticationException($e)) {
			$message = 'Unauthenticated or Token Expired, Please Login.';
			$data = [
				'success' => false,
				'message' => $message,
			];
			
			if (doesRequestIsFromWebApp($request) || isFromAjax($request)) {
				$data['error'] = $message; // for bootstrap-fileinput
			}
			
			return apiResponse()->json($data, Response::HTTP_UNAUTHORIZED);
		}
		
		// Throttle Requests Exception
		if ($this->isThrottleRequestsException($e)) {
			$data = [
				'success' => false,
				'message' => 'Too Many Requests, Please Slow Down.',
			];
			
			return apiResponse()->json($data, Response::HTTP_TOO_MANY_REQUESTS);
		}
		
		// Token Mismatch Exception
		if ($this->isTokenMismatchException($e)) {
			$message = t('session_expired_reload_needed');
			$data = [
				'success' => false,
				'message' => $message,
			];
			
			if (doesRequestIsFromWebApp($request) || isFromAjax($request)) {
				$data['error'] = $message; // for bootstrap-fileinput
			}
			
			return apiResponse()->json($data, Response::HTTP_UNAUTHORIZED);
		}
		
		// Validation Exception
		if ($this->isValidationException($e)) {
			$message = $e->getMessage();
			
			$data = [
				'success' => false,
				'message' => $message,
			];
			
			// Get validation error messages
			$errors = [];
			if (method_exists($e, 'errors')) {
				$errors = $e->errors();
				$data['errors'] = $errors;
			}
			
			if (doesRequestIsFromWebApp($request) || isFromAjax($request)) {
				// Get errors (as String)
				if (is_array($errors) && count($errors) > 0) {
					$errorsTxt = '';
					foreach ($errors as $value) {
						if (is_array($value)) {
							foreach ($value as $v) {
								$errorsTxt .= empty($errorsTxt) ? '- ' . $v : '<br>- ' . $v;
							}
						} else {
							$errorsTxt .= empty($errorsTxt) ? '- ' . $value : '<br>- ' . $value;
						}
					}
				} else {
					$errorsTxt = $message;
				}
				
				// NOTE: 'bootstrap-fileinput' need 'error' (text) element,
				// & the optional 'errorkeys' (array) element.
				$data['error'] = $errorsTxt; // for bootstrap-fileinput
			}
			
			return apiResponse()->json($data, Response::HTTP_UNPROCESSABLE_ENTITY);
		}
		
		// Error (Exception)
		if ($e instanceof \Error) {
			$message = $e->getMessage();
			if (empty($message)) {
				$message = 'There was some internal error.';
			}
			
			$data = [
				'success'   => false,
				'message'   => $message,
				'exception' => $e,
			];
			
			if (doesRequestIsFromWebApp($request) || isFromAjax($request)) {
				$data['error'] = $message; // for bootstrap-fileinput
			}
			
			return apiResponse()->json($data, 500);
		}
		
		// Other Exception
		
		// Get status code
		$status = (method_exists($e, 'getStatusCode')) ? $e->getStatusCode() : 500;
		$status = isValidHttpStatus($status) ? $status : 500;
		
		// Get error message
		$message = $e->getMessage();
		if (!empty($message)) {
			$message = !empty($e->getLine()) ? $message . ' Line: ' . $e->getLine() : $message;
			$message = !empty($e->getFile()) ? $message . ' in file: ' . $e->getFile() : $message;
		} else {
			$message = getHttpErrorMessage($status);
		}
		
		$data = [
			'success'   => false,
			'message'   => $message,
			'exception' => $e,
		];
		
		if (doesRequestIsFromWebApp($request) || isFromAjax($request)) {
			$data['error'] = $message; // for bootstrap-fileinput
		}
		
		return apiResponse()->json($data, $status);
	}
}
