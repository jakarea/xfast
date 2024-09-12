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

namespace App\Http\Controllers\Web\Public;

use App\Helpers\UrlGen;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Public\Traits\CommonTrait;
use App\Http\Controllers\Web\Public\Traits\EnvFileTrait;
use App\Http\Controllers\Web\Public\Traits\RobotsTxtTrait;
use App\Http\Controllers\Web\Public\Traits\SettingsTrait;
use App\Models\BusinessOwnerPermission;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Collection;

class FrontController extends Controller implements HasMiddleware
{
	use SettingsTrait, EnvFileTrait, RobotsTxtTrait, CommonTrait;

	public $request;
	public $data = [];
	protected Collection $userMenu;

	/**
	 * FrontController constructor.
	 */
	public function __construct()
	{
		// Set the storage disk
		$this->setStorageDisk();

		// Check & Change the App Key (If needed)
		$this->checkAndGenerateAppKey();

		// Load the Plugins
		$this->loadPlugins();

		// Check & Update the '/.env' file
		$this->checkDotEnvEntries();

		// Check & Update the '/public/robots.txt' file
		$this->checkRobotsTxtFile();

		// Load Localization Data first
		// Check out the SetCountryLocale Middleware
		$this->applyFrontSettings();

		// Get & Share Users Menu
		$this->userMenu = $this->getUserMenu();
		view()->share('userMenu', $this->userMenu);
	}

	/**
	 * Get the middleware that should be assigned to the controller.
	 */
	public static function middleware(): array
	{
		$array = [];

		// Check the 'Currency Exchange' plugin
		if (config('plugins.currencyexchange.installed')) {
			$array[] = 'currencies';
			$array[] = 'currencyExchange';
		}

		// Check the 'Domain Mapping' plugin
		if (config('plugins.domainmapping.installed')) {
			$array[] = 'domain.verification';
		}

		return $array;
	}

	/*
	 * Handle HTTP error for GET requests
	 */
	protected function handleHttpError(?array $data = [])
	{
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : null;

		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			$message = !empty($message) ? $message : 'Unknown Error.';
			$errorCode = (int)data_get($data, 'status');
			$errorCode = (strlen($errorCode) == 3) ? $errorCode : 400;

			abort($errorCode, $message);
		}

		return $message;
	}

	/*
	 * Handle HTTP error for non GET requests
	 * @todo: Check the redirect can be done externally
	 */
	protected function handleHttpErrorWithRedirect(?array $data = [], $withInput = [])
	{
		// Parsing the API response
		$message = !empty(data_get($data, 'message')) ? data_get($data, 'message') : 'Unknown Error.';

		// HTTP Error Found
		if (!data_get($data, 'isSuccessful')) {
			flash($message)->error();

			if (!empty($withInput)) {
				return redirect()->back()->withInput($withInput);
			} else {
				return redirect()->back();
			}
		}

		return $message;
	}

	/**
	 * @return \Illuminate\Support\Collection
	 */
	private function getUserMenu(): \Illuminate\Support\Collection
	{
		if (!auth()->check()) {
			return collect();
		}

		$authUser = auth()->user();

		$menuArray = [
			[
				'name'       => t('my_listings'),
				'url'        => url('account/posts/list'),
				'icon'       => 'fa-solid fa-list',
				'group'      => t('my_listings'),
				'countVar'   => 'posts.published',
				'inDropdown' => true,
				'isActive'   => (request()->segment(3) == 'list'),
			],
			[
				'name'       => t('pending_approval'),
				'url'        => url('account/posts/pending-approval'),
				'icon'       => 'fa-solid fa-hourglass-half',
				'group'      => t('my_listings'),
				'countVar'   => 'posts.pendingApproval',
				'inDropdown' => true,
				'isActive'   => (request()->segment(3) == 'pending-approval'),
			],
			[
				'name'       => t('archived_listings'),
				'url'        => url('account/posts/archived'),
				'icon'       => 'fa-solid fa-calendar-xmark',
				'group'      => t('my_listings'),
				'countVar'   => 'posts.archived',
				'inDropdown' => true,
				'isActive'   => (request()->segment(3) == 'archived'),
			],
			[
				'name'       => t('favourite_listings'),
				'url'        => url('account/posts/favourite'),
				'icon'       => 'fa-solid fa-bookmark',
				'group'      => t('my_listings'),
				'countVar'   => 'posts.favourite',
				'inDropdown' => true,
				'isActive'   => (request()->segment(3) == 'favourite'),
			],
			[
				'name'             => t('messenger'),
				'url'              => url('account/messages'),
				'icon'             => 'fa-regular fa-envelope',
				'group'            => t('my_listings'),
				'countVar'         => 0,
				'countCustomClass' => ' count-threads-with-new-messages',
				'inDropdown'       => true,
				'isActive'         => (request()->segment(2) == 'messages'),
			],
			[
				'name'       => t('Saved searches'),
				'url'        => url('account/saved-searches'),
				'icon'       => 'fa-solid fa-bell',
				'group'      => t('my_listings'),
				'countVar'   => 'savedSearch',
				'inDropdown' => true,
				'isActive'   => (request()->segment(2) == 'saved-searches'),
			],
			[
				'name'       => t('promotion'),
				'url'        => url('account/transactions/promotion'),
				'icon'       => 'fa-solid fa-coins',
				'group'      => t('Transactions'),
				'countVar'   => 'transactions.promotion',
				'inDropdown' => false,
				'isActive'   => (request()->segment(2) == 'transactions' && request()->segment(3) == 'promotion'),
			],
			[
				'name'       => t('subscription'),
				'url'        => url('account/transactions/subscription'),
				'icon'       => 'fa-solid fa-coins',
				'group'      => t('Transactions'),
				'countVar'   => 'transactions.subscription',
				'inDropdown' => false,
				'isActive'   => (request()->segment(2) == 'transactions' && request()->segment(3) == 'subscription'),
			],
			[
				'name'       => t('My Account'),
				'url'        => url('account'),
				'icon'       => 'fa-solid fa-gear',
				'group'      => t('My Account'),
				'countVar'   => null,
				'inDropdown' => true,
				'isActive'   => (request()->segment(1) == 'account' && request()->segment(2) == null),
			],
			[
				'name'       => t('staff_list'),
				'url'        => url('staff-management/list'),
				'icon'       => 'fa-solid fa-gears',
				'group'      => t('staff_list'),
				'countVar'   => null,
				'inDropdown' => true,
				'isActive'   => (request()->segment(1) == 'staff-management' && request()->segment(2) == 'list'),

			],
		];
    
     if ($authUser->business == 1) {
            $menuArray[] = [
                'name' => t('Business Information'),
                'url' => url('account/business'),
                'icon' => 'fas fa-suitcase',
                'group' => t('My Account'),
                'countVar' => null,
                'inDropdown' => true,
                'isActive' => (request()->segment(1) == 'account' && request()->segment(2) == 'business'),
            ];
        }

		if (app('impersonate')->isImpersonating()) {
			$logOut = [
				'name'       => t('Leave'),
				'url'        => route('impersonate.leave'),
				'icon'       => 'fa-solid fa-right-from-bracket',
				'group'      => t('My Account'),
				'countVar'   => null,
				'inDropdown' => true,
				'isActive'   => false,
			];
		} else {
			$logOut = [
				'name'       => t('log_out'),
				'url'        => UrlGen::logout(),
				'icon'       => 'fa-solid fa-right-from-bracket',
				'group'      => t('My Account'),
				'countVar'   => null,
				'inDropdown' => true,
				'isActive'   => false,
			];
		}

		$closeAccount = [
			'name'       => t('Close account'),
			'url'        => url('account/close'),
			'icon'       => 'fa-solid fa-circle-xmark',
			'group'      => t('My Account'),
			'countVar'   => null,
			'inDropdown' => false,
			'isActive'   => (request()->segment(2) == 'close'),
		];

		$adminPanel = [];
		if (doesUserHavePermission($authUser, Permission::getStaffPermissions())) {
			$adminPanel = [
				'name'       => t('admin_panel'),
				'url'        => admin_url('/'),
				'icon'       => 'fa-solid fa-gears',
				'group'      => t('admin_panel'),
				'countVar'   => null,
				'inDropdown' => true,
				'isActive'   => false,
			];
		}

		// staf manage route 
		$staffManage = []; 
		if (hasOwnerPermission(auth()->id(), 'staff_info_manage')) {
			$staffManage =  [
				'name'       => t('Add Staff'),
				'url'        => url('staff-management/add'),
				'icon'       => 'fa-solid fa-gear',
				'group'      => t('staff_list'),
				'countVar'   => null,
				'inDropdown' => true,
				'isActive'   => (request()->segment(1) == 'staff-management' && request()->segment(2) == 'add'),
			];
		}  


		if (!empty($adminPanel)) { 
			if (!empty($staffManage)) {
				array_push($menuArray, $logOut, $staffManage, $closeAccount);
			} else { 
				array_push($menuArray, $logOut, $closeAccount, $adminPanel);
			}

		} else {

			if (!empty($staffManage)) {
				array_push($menuArray, $logOut, $staffManage, $closeAccount);
			} else { 
				array_push($menuArray, $logOut, $closeAccount);
			} 
		}

		// Set missed information
		return collect($menuArray)->map(function ($item, $key) {
			// countCustomClass
			$item['countCustomClass'] = (isset($item['countCustomClass'])) ? $item['countCustomClass'] : '';

			// path
			$matches = [];
			preg_match('|(account.*)|ui', $item['url'], $matches);
			$item['path'] = $matches[1] ?? '-1';
			$item['path'] = str_replace(['account', '/'], '', $item['path']);

			return $item;
		});
	}
}
