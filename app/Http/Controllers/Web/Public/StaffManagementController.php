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

use App\Http\Controllers\Web\Public\Account\AccountBaseController;
use App\Models\Gender; 
use App\Models\User; 
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Front\UserRequest;
use App\Models\Role; 
use App\Http\Resources\UserResource;
use App\Models\BusinessOwnerPermission;
use App\Models\Scopes\VerifiedScope; 
use Illuminate\Support\Facades\Auth;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class StaffManagementController extends AccountBaseController
{  

	/**
	 * StaffManagementController constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * @return \Illuminate\Contracts\View\View
	 */
	// retrive only business owner staff list
	public function index()
	{ 
		$staffs = [];
		$staffs = User::with('roles')
		->where('owner_id',Auth::id())
        ->whereHas('roles', function ($query) {
            $query->where('name', 'owner-staff');
        })
        ->get();


		return appView('staff.index',compact('staffs'));
	}

	public function add()
	{ 
		return appView('staff.add');
	}

	public function store(UserRequest $request)
	{ 
		  // Conditions to Verify User's Email or Phone
		  $emailOrPhone = $request->input('email');
		  if (filter_var($emailOrPhone, FILTER_VALIDATE_EMAIL)) {
			  $email = $emailOrPhone;
			  $request->merge(['email' => $email]);  // Update the request data
			  $request->merge(['phone' => null]);    // Set phone number to null if email is provided
		  } elseif (preg_match('/^[0-9]{10}$/', $emailOrPhone)) {
			  $phoneNumber = $emailOrPhone;
			  $request->merge(['phone' => $phoneNumber]);  // Update the request data
			  $request->merge(['email' => null]);          // Set email to null if phone number is provided
		  }
		  $emailVerificationRequired = config('settings.mail.email_verification') == '1' && $request->filled('email');
		  $phoneVerificationRequired = config('settings.sms.phone_verification') == '1' && $request->filled('phone');
		  
		  // New User
		  $user = new User();
		  $input = $request->only($user->getFillable());
		  foreach ($input as $key => $value) {
			  if ($request->has($key)) {
				  $user->{$key} = $value;
			  }
		  }
		  
		  if ($request->filled('password')) {
			  if (isset($input['password'])) {
				  $user->password = Hash::make($input['password']);
			  }
		  }
		  
		  if ($request->anyFilled(['email', 'phone'])) {
			  $user->email_verified_at = now();
			  $user->phone_verified_at = now(); 
		  }

		  $user->owner_id = Auth::id();
		  
		  // Save
		  $user->save();
		  $role = Role::where('name', 'owner-staff')->first();
		  $user->assignRole($role);

		  session()->flash('Staff Addedd success');
		  return redirect('staff-management/list'); 
		
	} 

	public function edit($id)
	{ 
		if (!$id) {
			return redirect()->back();
		}

		$authUser = User::find($id);

		$genders = $this->gender();
		return appView('staff.edit',compact('genders','authUser'));
	}

	public function update(UserRequest $request)
	{
		$id = $request->staff_id; 

		$this->updateStaffDetails($id, $request);
		return redirect('staff-management/list')->with('success','User updated Success');
	}

	// PRIVATE METHODS

	private function updateStaffDetails($id, $request)
	{ 
		$user = User::where('id', $id)->first();

		// add or update user add numbers
		BusinessOwnerPermission::updateOrCreate(
			[
				'username' => $user['username'],
				'key' => 'number_of_add'
			],
			[
				'owner_id' => $user['id'],
				'value' => $request['number_of_add'],
				'status' => 1
			]
		);
		
		if (empty($user)) {
			return apiResponse()->notFound(t('user_not_found'));
		}
		
		// $authUser = request()->user() ?? auth('sanctum')->user();
		// if (empty($authUser)) {
		// 	return apiResponse()->unauthorized();
		// }
		
		// // Check logged User
		// // Get the User Personal Access Token Object
		// $personalAccess = $authUser->tokens()->where('id', getApiAuthToken())->first();
		// if (!empty($personalAccess)) {
		// 	if ($personalAccess->tokenable_id != $user->id) {
		// 		return apiResponse()->unauthorized();
		// 	}
		// } else {
		// 	return apiResponse()->unauthorized();
		// }
		
		// Check if these fields have changed
		$emailChanged = $request->filled('email') && $request->input('email') != $user->email;
		$phoneChanged = $request->filled('phone') && $request->input('phone') != $user->phone;
		$usernameChanged = $request->filled('username') && $request->input('username') != $user->username;
		
		// Conditions to Verify User's Email or Phone
		$emailVerificationRequired = config('settings.mail.email_verification') == '1' && $emailChanged;
		$phoneVerificationRequired = config('settings.sms.phone_verification') == '1' && $phoneChanged;
		
		// Update User
		$input = $request->only($user->getFillable());
		
		$protectedColumns = ['username', 'password'];
		$protectedColumns = ($request->filled('auth_field'))
			? array_merge($protectedColumns, [$request->input('auth_field')])
			: array_merge($protectedColumns, ['email', 'phone']);
		
		foreach ($input as $key => $value) {
			if ($request->has($key)) {
				if (in_array($key, $protectedColumns) && empty($value)) {
					continue;
				}
				
				if ($key == 'photo' && isUploadedFile($value)) {
					continue;
				}
				
				$user->{$key} = $value;
			}
		}
		
		// Checkboxes
		$user->phone_hidden = (int)$request->input('phone_hidden');
		$user->disable_comments = (int)$request->input('disable_comments');
		$user->accept_marketing_offers = (int)$request->input('accept_marketing_offers');
		if ($request->filled('accept_terms')) {
			$user->accept_terms = (int)$request->input('accept_terms');
		}
				
		// Other fields
		if ($request->filled('password')) {
			if (isset($input['password'])) {
				$user->password = Hash::make($input['password']);
			}
		} 
		
		// Save
		$user->save(); 
		 
		
		// User's Photo
		// $extra['photo'] = [];
		// if ($request->hasFile('photo')) {
		// 	// Update User's Photo
		// 	$extra['photo'] = $this->updateUserPhoto($user->id, $request)->getData(true);
		// } else {
		// 	// Remove User's Photo
		// 	$photoRemovalRequested = ($request->filled('remove_photo') && $request->input('remove_photo'));
		// 	if ($photoRemovalRequested) {
		// 		$extra['photo'] = $this->removeUserPhoto($user->id, $request)->getData(true);
		// 	}
		// }
		// if (array_key_exists('success', $extra['photo'])) {
		// 	// Update the '$data' result value If a photo is uploaded successfully
		// 	if ($extra['photo']['success']) {
		// 		if (!empty($extra['photo']['result'])) {
		// 			$data['result'] = $extra['photo']['result'];
		// 			unset($extra['photo']['result']);
		// 		}
		// 	}
			
		// 	// Update the '$data' infos If error found during the photo upload
		// 	if (!$extra['photo']['success']) {
		// 		if (array_key_exists('message', $extra['photo'])) {
		// 			$data['success'] = $extra['photo']['success'];
		// 			$data['message'] = $extra['photo']['message'];
		// 			unset($extra['photo']['success']);
		// 			unset($extra['photo']['message']);
		// 		}
		// 	}
		// } 

		return $user;
 
	}

		/**
	 * Delete user
	 *
	 * @authenticated
	 * @header Authorization Bearer {YOUR_AUTH_TOKEN}
	 *
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function destroy($id) 
	{
		 if (!$id) {
			 return back()->with('error','Invalid URL');
		 }

		 $user = User::find($id);

		 if ($user) {
			 $user->delete();
			 return back()->with('success','Staff Deleted Success');
		 }

		 return back()->with('error','Deleted Failed');
	}
	
	/**
	 * @return array
	 */
	private function gender()
	{
		return Gender::query()->get(); 
	}
}
