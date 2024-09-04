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

return [
	
	// MEDIA PATH
	// Default Logos
	'logo'           => 'app/default/logo.png',
	'logo-dark'      => 'app/default/logo-dark.png',
	'logo-light'     => 'app/default/logo-light.png',
	
	// Default Icons
	'favicon'        => 'app/default/ico/favicon.png',
	
	// Default Pictures
	'picture'        => 'app/default/picture.jpg',
	'avatar'         => 'app/default/user.png',
	'company-logo'   => 'app/default/picture.jpg',
	
	// MEDIA RESIZE
	/*
	 * Media Resize Default Parameters
	 *
	 * Note:
	 * The system types of resize below are not available in the 'Upload' options in the Admin Panel
	 * - logo-max,
	 * - cat,
	 * - bg-header, bg-body,
	 * - avatar, company-logo,
	 */
	'resize'         => [
		'methods'   => [
			'resize',
			'fit',
			'resizeCanvas',
		],
		'positions' => ['top-left', 'top', 'top-right', 'left', 'center', 'right', 'bottom-left', 'bottom', 'bottom-right'],
		'namedOptions'     => [
			'default'        => [
				'width'    => 1500,
				'height'   => 1500,
				'method'   => 'resize',
				'ratio'    => '1',
				'upsize'   => '0',
				'position' => 'center',
				'relative' => false,
				'bgColor'  => 'ffffff',
			],
			
			// logo
			'logo'           => [
				'width'    => 485, // 216|485,
				'height'   => 90,  // 40|90,
				'method'   => 'resize',
				'ratio'    => '1',
				'upsize'   => '0',
				'position' => 'center',
				'relative' => false,
				'bgColor'  => 'rgba(0, 0, 0, 0)',
			],
			'logo-max'       => [ // Used in CSS styles
				'width'    => 430,
				'height'   => 80,
				'method'   => 'resize',
				'ratio'    => '1',
				'upsize'   => '0',
				'position' => 'center',
				'relative' => false,
				'bgColor'  => 'rgba(0, 0, 0, 0)',
			],
			
			// icon
			'favicon'   => [
				'width'    => 32,
				'height'   => 32,
				'method'   => 'resize',
				'ratio'    => '1',
				'upsize'   => '0',
				'position' => 'center',
				'relative' => false,
				'bgColor'  => 'rgba(0, 0, 0, 0)',
			],
			
			// asset
			'cat'            => [
				'width'    => 70,
				'height'   => 70,
				'method'   => 'resize',
				'ratio'    => '1',
				'upsize'   => '0',
				'position' => 'center',
				'relative' => false,
				'bgColor'  => 'rgba(0, 0, 0, 0)',
			],
			'bg-header'      => [
				'width'    => 2000,
				'height'   => 1000,
				'method'   => 'resize',
				'ratio'    => '1',
				'upsize'   => '0',
				'position' => 'center',
				'relative' => false,
				'bgColor'  => 'ffffff',
			],
			'bg-body'        => [
				'width'    => 2500,
				'height'   => 2500,
				'method'   => 'resize',
				'ratio'    => '1',
				'upsize'   => '0',
				'position' => 'center',
				'relative' => false,
				'bgColor'  => 'ffffff',
			],
			
			// picture
			'picture-sm'     => [
				'label'    => 'small', // Local key or label
				'width'    => 120,
				'height'   => 90,
				'method'   => 'resizeCanvas',
				'ratio'    => '1',
				'upsize'   => '0',
				'position' => 'center',
				'relative' => false,
				'bgColor'  => 'ffffff',
			],
			'picture-md'     => [
				'label'    => 'medium',
				'width'    => 320,
				'height'   => 240,
				'method'   => 'fit',
				'ratio'    => '1',
				'upsize'   => '0',
				'position' => 'center',
				'relative' => false,
				'bgColor'  => 'ffffff',
			],
			'picture-lg'     => [
				'label'    => 'large',
				'width'    => 816,
				'height'   => 460,
				'method'   => 'resize',
				'ratio'    => '1',
				'upsize'   => '0',
				'position' => 'center',
				'relative' => false,
				'bgColor'  => 'ffffff',
			],
			
			// avatar
			'avatar'         => [
				'width'    => 800,
				'height'   => 800,
				'method'   => 'resize',
				'ratio'    => '1',
				'upsize'   => '0',
				'position' => 'center',
				'relative' => false,
				'bgColor'  => 'ffffff',
			],
			
			// company
			'company-logo'   => [
				'width'    => 800,
				'height'   => 800,
				'method'   => 'resize',
				'ratio'    => '1',
				'upsize'   => '0',
				'position' => 'center',
				'relative' => false,
				'bgColor'  => 'rgba(0, 0, 0, 0)',
			],
		],
	],
	
	'versioned' => env('PICTURE_VERSIONED', false),
	'version'   => env('PICTURE_VERSION', 1),

];
