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

use App\Http\Controllers\Web\Public\Auth\Traits\VerificationTrait;
use App\Models\BusinessAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Larapen\LaravelMetaTags\Facades\MetaTag;

class BusinessAccountController extends AccountBaseController
{
    use VerificationTrait;

    public function index()
    {

    }

    public function create()
    {
        $business = BusinessAccount::where('user_id', Auth::id())->first();

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
        /*dd($request->all());*/

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
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cover_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'company_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'company_videos.*' => 'nullable|mimes:mp4,mov,avi,wmv|max:10000',
            'social_media_links.*' => 'nullable|url|max:255',
        ]);
        $business = BusinessAccount::where('user_id', Auth::id())->first();
        if (!$business) {
            $business = new BusinessAccount($validatedData);
            $business->user_id = auth()->id();
        } else {
            $business->update($validatedData);
        }

        if ($request->hasFile('logo')) {
            $business->logo = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('cover_photo')) {
            $business->cover_photo = $request->file('cover_photo')->store('cover_photos', 'public');
        }

        $existingImages = $business->company_images ?? [];
        // Handle new images
        if ($request->hasFile('company_images')) {
            foreach ($request->file('company_images') as $image) {
                $path = $image->store('company_images', 'public');
                $existingImages[] = $path;  // Add the new image to the existing images
            }
            $business->company_images = $existingImages;
        }


        $existingVideos = $business->company_videos ?? [];
        if ($request->hasFile('company_videos')) {
            foreach ($request->file('company_videos') as $video) {
                $videos = $video->store('company_videos', 'public');
                $existingVideos[] = $videos;
            }
            $business->company_videos = $existingVideos;
        }

        $socialMediaLinks = $request->input('social_media_links', []);
        $business->social_media_links = array_filter($socialMediaLinks);

        $business->save();
        flash("Business information saved successfully")->success();
        return redirect()->back();
    }

    public function removeCompanyImage($id, $index, Request $request)
    {
        $business = BusinessAccount::findOrFail($id);

        $companyImages = $business->company_images;

        if (isset($companyImages[$index])) {
            $imagePath = $companyImages[$index];

            if (Storage::exists('public/' . $imagePath)) {
                Storage::delete('public/' . $imagePath);
            }

            unset($companyImages[$index]);

            $business->company_images = array_values($companyImages);
            $business->save();

            return response()->json(['success' => true, 'message' => 'Image removed successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Image not found'], 404);
    }

    public function removeCompanyVideo($id, $index, Request $request)
    {
        $business = BusinessAccount::findOrFail($id);

        $companyVideos = $business->company_videos;

        if (isset($companyVideos[$index])) {
            $videoPath = $companyVideos[$index];

            if (Storage::exists('public/' . $videoPath)) {
                Storage::delete('public/' . $videoPath);
            }

            unset($companyVideos[$index]);

            $business->company_videos = array_values($companyVideos);
            $business->save();

            return response()->json(['success' => true, 'message' => 'Image removed successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Image not found'], 404);
    }

    public function switchProfile(Request $request)
    {
        $status = $request->input('business');
        $user = User::findOrFail(Auth::id());
        if ($user) {
            $user->update([
                'business' => $status == 1 ? 1 : 0
            ]);
            if ($status == 1) {
                return redirect()->to(url('account/business'));
            }else{
                return redirect()->to(url('account'));
            }

        } else {
            return false;
        }
    }
}
