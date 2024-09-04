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

namespace App\Helpers\Response;

Class Ajax
{
	/**
	 * @param array|null $data
	 * @param int $status
	 * @param array $headers
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function json(?array $data = [], int $status = 200, array $headers = []): \Illuminate\Http\JsonResponse
	{
		$data = is_array($data) ? $data : [];
		
		$status = isValidHttpStatus($status) ? $status : 500;
		$headers = addContentTypeHeader('application/json', $headers);
		
		return response()->json($data, $status, $headers, JSON_UNESCAPED_UNICODE);
	}
	
	/**
	 * @param string|null $content
	 * @param int $status
	 * @param array $headers
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
	 */
	public function text(?string $content = '', int $status = 200, array $headers = [])
	{
		$content = is_string($content) ? $content : '';
		
		$status = isValidHttpStatus($status) ? $status : 500;
		$headers = addContentTypeHeader('text/plain', $headers);
		
		return response($content, $status)->withHeaders($headers);
	}
}
