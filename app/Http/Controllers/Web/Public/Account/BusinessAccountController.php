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

namespace App\Http\Controllers\Web\Public\Account;

use App\Http\Controllers\Controller;
use App\Models\BusinessAccount;
use App\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class BusinessAccountController extends Controller
{
    public function index()
    {

    }

    public function create()
    {
        $business= BusinessAccount::where('user_id',Auth::id())->first();

        $appName = config('settings.app.name', 'Site Name');
        $title = t('my_account') . ' - ' . $appName;

        // Meta Tags
        MetaTag::set('title', $title);
        MetaTag::set('description', t('my_account_on', ['appName' => config('settings.app.name')]));

        $authUser = request()->user() ?? auth('sanctum')->user();
        return appView('account.business.business_form', compact('business', 'authUser'));
    }

    public function store(Request $request)
    {
        //dd($request->all());

        $validatedData = $request->validate([
            'business_name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'business_title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone1' => 'required|string|max:20',
            'phone2' => 'nullable|string|max:20',
            'whatsapp' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'website' => 'nullable|url|max:255',
            'product_services' => 'required|string',
            'business_description' => 'required|string',
            /*'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cover_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'company_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'company_videos.*' => 'nullable|mimes:mp4,mov,avi,wmv|max:10000',
            'social_media_links.*' => 'nullable|url|max:255',*/
        ]);

        $business = new BusinessAccount($validatedData);
        $business->user_id = auth()->id();

        if ($request->hasFile('logo')) {
            $business->logo = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('cover_photo')) {
            $business->cover_photo = $request->file('cover_photo')->store('cover_photos', 'public');
        }

        if ($request->hasFile('company_images')) {
            $images = [];
            foreach ($request->file('company_images') as $image) {
                $images[] = $image->store('company_images', 'public');
            }
            $business->company_images = $images;
        }

        if ($request->hasFile('company_videos')) {
            $videos = [];
            foreach ($request->file('company_videos') as $video) {
                $videos[] = $video->store('company_videos', 'public');
            }
            $business->company_videos = $videos;
        }

        $business->social_media_links = $request->input('social_media_links');

        $business->save();

        return redirect()->back()->with('success', 'Business information saved successfully.');
    }
}
