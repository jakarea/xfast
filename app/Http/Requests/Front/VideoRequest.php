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

namespace App\Http\Requests\Front;

use App\Http\Requests\Request;

class VideoRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        // Require 'videos' if they exist in the request
        if ($this->file('videos')) {
            $files = $this->file('videos');
            foreach ($files as $key => $file) {
                if (empty($file)) continue;

                $rules['videos.' . $key] = [
                    'mimes:' . getUploadFileTypes('video'),  // Validate allowed video file types
                    'min:' . (int)config('settings.upload.min_video_size', 0),
                    'max:' . (int)config('settings.upload.max_video_size', 50000),  // Set maximum size (in KB)
                ];
            }
        }

        // Apply this rule only for the 'Multi Steps Form' Web-based requests
        if (!isFromApi()) {
            // Check if this request comes from Listing creation form
            if (session()->has('postInput')) {
                // If no video is uploaded & If video is mandatory,
                // Don't allow user to go to the next page.
                $videosInput = (array)session('videosInput');
                if (empty($videosInput)) {
                    if (config('settings.single.video_mandatory')) {
                        $rules['videos'] = ['required'];
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = [];

        if ($this->file('videos')) {
            $files = $this->file('videos');
            foreach ($files as $key => $file) {
                $attributes['videos.' . $key] = t('video X', ['key' => ($key + 1)]);
            }
        }

        return $attributes;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        $messages = [];

        if ($this->file('videos')) {
            $files = $this->file('videos');
            foreach ($files as $key => $file) {
                // uploaded
                $maxSize = (int)config('settings.upload.max_video_size', 50000); // In KB (50MB default)
                $maxSize = $maxSize * 1024; // Convert KB to Bytes
                $msg = t('large_file_uploaded_error', [
                    'field'   => t('video X', ['key' => ($key + 1)]),
                    'maxSize' => readableBytes($maxSize),
                ]);

                $uploadMaxFilesizeStr = @ini_get('upload_max_filesize');
                $postMaxSizeStr = @ini_get('post_max_size');
                if (!empty($uploadMaxFilesizeStr) && !empty($postMaxSizeStr)) {
                    $uploadMaxFilesize = forceToInt($uploadMaxFilesizeStr);
                    $postMaxSize = forceToInt($postMaxSizeStr);

                    $serverMaxSize = min($uploadMaxFilesize, $postMaxSize);
                    $serverMaxSize = $serverMaxSize * 1024 * 1024; // Convert MB to KB to Bytes
                    if ($serverMaxSize < $maxSize) {
                        $msg = t('large_file_uploaded_error_system', [
                            'field'   => t('video X', ['key' => ($key + 1)]),
                            'maxSize' => readableBytes($serverMaxSize),
                        ]);
                    }
                }

                $messages['videos.' . $key . '.uploaded'] = $msg;
            }
        }

        if (config('settings.single.video_mandatory')) {
            $messages['videos.required'] = t('videos_mandatory_text');
        }

        return $messages;
    }
}
