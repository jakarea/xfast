{{--
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
--}}
@extends('layouts.master')

@php
    $authUserIsAdmin ??= false;
    $userStats ??= [];

    $fiTheme = config('larapen.core.fileinput.theme', 'bs5');
@endphp

@section('content')
    @includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
    <div class="main-container">
        <div class="container">
            <div class="row">
                <div class="col-md-3 page-sidebar">
                    @includeFirst([config('larapen.core.customizedViewPath') . 'account.inc.sidebar', 'account.inc.sidebar'])
                </div>

                <div class="col-md-9 page-content">

                    @include('flash::message')

                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="{{ t('Close') }}"></button>
                            <h5><strong>{{ t('oops_an_error_has_occurred') }}</strong></h5>
                            <ul class="list list-check">
                                @foreach ($errors->all() as $error)
                                    <li>{!! $error !!}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div id="avatarUploadError" class="center-block" style="width:100%; display:none"></div>
                    <div id="avatarUploadSuccess" class="alert alert-success fade show" style="display:none;"></div>


                    <div class="inner-box default-inner-box" style="overflow: visible;">
                        <div class="row">
                            <div class="col-8">
                                <div class="welcome-msg">
                                    <h3 class="page-sub-header2 clearfix no-padding">{{ t('Hello') }} {{ $authUser->name }}
                                        ! </h3>
                                    <span class="page-sub-header-sub small">
		                                {{ t('You last logged in at') }}: {{ \App\Helpers\Date::format($authUser->last_login_at, 'datetime') }}
		                            </span>
                                </div>
                            </div>
                            <div class="col-4 d-flex align-items-center justify-content-end">
                                @if (config('settings.app.dark_mode') == '1')
                                    @php
                                        $themeSwitcherActive = isDarkModeEnabledForCurrentUser() ? ' active' : '';
                                    @endphp
                                    <label class="theme-switcher theme-switcher-left-right{{ $themeSwitcherActive }}"
                                           data-user-id="{{ $authUser->id }}"
                                    >
										<span class="theme-switcher-label"
                                              data-on="{{ t('dark_mode_on') }}"
                                              data-off="{{ t('dark_mode_off') }}"
                                        ></span>
                                        <span class="theme-switcher-handle"></span>
                                    </label>
                                @endif
                            </div>
                        </div>

                        <div id="accordion" class="panel-group">

                            <form name="details"
                                  class="form-horizontal"
                                  role="form"
                                  method="POST"
                                  action="{{ route('business.store') }}"
                                  enctype="multipart/form-data"
                            >
                                {!! csrf_field() !!}

                                {{-- Business Information --}}
                                <div class="card card-default">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            <a href="#userPanel" aria-expanded="true" data-bs-toggle="collapse"
                                               data-parent="#accordion">
                                                {{ t('business_details') }}
                                            </a>
                                        </h4>
                                    </div>
                                    @php
                                        $userPanelClass = '';
                                        $userPanelClass = request()->filled('panel')
                                            ? (request()->query('panel') == 'user' ? 'show' : $userPanelClass)
                                            : ((old('panel') == '' || old('panel') == 'user') ? 'show' : $userPanelClass);
                                    @endphp
                                    <div class="panel-collapse collapse {{ $userPanelClass }}" id="userPanel">
                                        <div class="card-body">


                                            <?php $nameError = (isset($errors) && $errors->has('business_name')) ? ' is-invalid' : ''; ?>
                                            <div class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $nameError }}"
                                                       for="business_name">{{ t('Business Name') }} <sup>*</sup></label>
                                                <div class="col-md-9 col-lg-8 col-xl-6">
                                                    <input name="business_name"
                                                           type="text"
                                                           class="form-control{{ $nameError }}"
                                                           placeholder=""
                                                           value="{{ old('business_name', isset($business) ? $business->business_name : '') }}"
                                                    >
                                                </div>
                                            </div>

                                            <?php $categoryError = (isset($errors) && $errors->has('category')) ? ' is-invalid' : ''; ?>
                                            <div class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $categoryError }}"
                                                       for="category">{{ t('Category') }} <sup>*</sup></label>
                                                <div class="col-md-9 col-lg-8 col-xl-6">
                                                    <input name="category"
                                                           type="text"
                                                           class="form-control{{ $categoryError }}"
                                                           placeholder=""
                                                           value="{{ old('category', isset($business) ? $business->category : '') }}"
                                                    >
                                                </div>
                                            </div>

                                            <?php $titleError = (isset($errors) && $errors->has('business_title')) ? ' is-invalid' : ''; ?>
                                            <div class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $titleError }}"
                                                       for="business_title">{{ t('Business Title') }}
                                                    <sup>*</sup></label>
                                                <div class="col-md-9 col-lg-8 col-xl-6">
                                                    <input name="business_title"
                                                           type="text"
                                                           class="form-control{{ $titleError }}"
                                                           placeholder=""
                                                           value="{{ old('business_title', isset($business) ? $business->business_title : '') }}"
                                                    >
                                                </div>
                                            </div>

                                            <?php $locationError = (isset($errors) && $errors->has('location')) ? ' is-invalid' : ''; ?>
                                            <div class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $locationError }}"
                                                       for="location">{{ t('Location') }} <sup>*</sup></label>
                                                <div class="col-md-9 col-lg-8 col-xl-6">
                                                    <input name="location"
                                                           type="text"
                                                           class="form-control{{ $locationError }}"
                                                           placeholder=""
                                                           value="{{ old('location', isset($business) ? $business->location : '') }}"
                                                    >
                                                </div>
                                            </div>

                                            <?php $phone1Error = (isset($errors) && $errors->has('phone1')) ? ' is-invalid' : ''; ?>
                                            <div class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $phone1Error }}"
                                                       for="phone1">{{ t('Phone1') }} <sup>*</sup></label>
                                                <div class="col-md-9 col-lg-8 col-xl-6">
                                                    <input name="phone1"
                                                           type="text"
                                                           class="form-control{{ $phone1Error }}"
                                                           placeholder=""
                                                           value="{{ old('phone1', isset($business) ? $business->phone1 : '') }}"
                                                    >
                                                </div>
                                            </div>

                                            <?php $phone2Error = (isset($errors) && $errors->has('phone2')) ? ' is-invalid' : ''; ?>
                                            <div class="row mb-3">
                                                <label class="col-md-3 col-form-label{{ $phone2Error }}"
                                                       for="phone2">{{ t('Phone2') }}</label>
                                                <div class="col-md-9 col-lg-8 col-xl-6">
                                                    <input name="phone2"
                                                           type="text"
                                                           class="form-control{{ $phone2Error }}"
                                                           placeholder=""
                                                           value="{{ old('phone2', isset($business) ? $business->phone2 : '') }}"
                                                    >
                                                </div>
                                            </div>

                                            <?php $whatsappError = (isset($errors) && $errors->has('whatsapp')) ? ' is-invalid' : ''; ?>
                                            <div class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $whatsappError }}"
                                                       for="whatsapp">{{ t('WhatsApp') }} <sup>*</sup></label>
                                                <div class="col-md-9 col-lg-8 col-xl-6">
                                                    <input name="whatsapp"
                                                           type="text"
                                                           class="form-control{{ $whatsappError }}"
                                                           placeholder=""
                                                           value="{{ old('whatsapp', isset($business) ? $business->whatsapp : '') }}"
                                                    >
                                                </div>
                                            </div>

                                            <?php $emailError = (isset($errors) && $errors->has('email')) ? ' is-invalid' : ''; ?>
                                            <div class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $emailError }}"
                                                       for="email">{{ t('Email') }} <sup>*</sup></label>
                                                <div class="col-md-9 col-lg-8 col-xl-6">
                                                    <input name="email"
                                                           type="email"
                                                           class="form-control{{ $emailError }}"
                                                           placeholder=""
                                                           value="{{ old('email', isset($business) ? $business->email : '') }}"
                                                    >
                                                </div>
                                            </div>

                                            <?php $websiteError = (isset($errors) && $errors->has('website')) ? ' is-invalid' : ''; ?>
                                            <div class="row mb-3">
                                                <label class="col-md-3 col-form-label{{ $websiteError }}"
                                                       for="website">{{ t('Website') }}</label>
                                                <div class="col-md-9 col-lg-8 col-xl-6">
                                                    <input name="website"
                                                           type="url"
                                                           class="form-control{{ $websiteError }}"
                                                           placeholder=""
                                                           value="{{ old('website', isset($business) ? $business->website : '') }}"
                                                    >
                                                </div>
                                            </div>

                                            <?php $productsError = (isset($errors) && $errors->has('product_services')) ? ' is-invalid' : ''; ?>
                                            <div class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $productsError }}"
                                                       for="product_services">{{ t('Product/Services') }}
                                                    <sup>*</sup></label>
                                                <div class="col-md-9 col-lg-8 col-xl-6">
                                            <textarea name="product_services"
                          class="form-control{{ $productsError }}"
                          rows="4">{{ old('product_services', isset($business) ? $business->product_services : '') }}
                                            </textarea>
                                                </div>
                                            </div>

                                            <?php $descriptionError = (isset($errors) && $errors->has('business_description')) ? ' is-invalid' : ''; ?>
                                            <div class="row mb-3 required">
                                                <label class="col-md-3 col-form-label{{ $descriptionError }}"
                                                       for="business_description">{{ t('Business Description') }}
                                                    <sup>*</sup></label>
                                                <div class="col-md-9 col-lg-8 col-xl-6">
                                    <textarea name="business_description"
                          class="form-control{{ $descriptionError }}"
                          rows="4">{{ old('business_description', isset($business) ? $business->business_description : '') }}
                                    </textarea>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>

                                {{-- Logo --}}
                                <div class="card card-default">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            <a href="#photoPanel" data-bs-toggle="collapse"
                                               data-parent="#accordion">{{ t('Logo') }}</a>
                                        </h4>
                                    </div>

                                    @php
                                        $photoPanelClass = '';
                                        $photoPanelClass = request()->filled('panel')
                                            ? (request()->query('panel') == 'logo' ? 'show' : $photoPanelClass)
                                            : ((old('panel')=='' || old('panel') =='logo') ? 'show' : $photoPanelClass);
                                    @endphp
                                    <div class="panel-collapse collapse {{ $photoPanelClass }}" id="photoPanel">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xl-12 text-center">
                                                    @php
                                                        $photoError = (isset($errors) && $errors->has('logo')) ? ' is-invalid' : '';
                                                    @endphp
                                                    <div class="photo-field">
                                                        <div class="file-loading">
                                                            <input id="logoField" name="logo" type="file"
                                                                   class="file {{ $photoError }}"
                                                                   value="{{ old('logo', isset($business) ? $business->logo : '') }}"
                                                            >
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- cover photo --}}
                                <div class="card card-default mt-2">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            <a href="#photoPanel" data-bs-toggle="collapse"
                                               data-parent="#accordion">{{ t('Cover Photo') }}</a>
                                        </h4>
                                    </div>

                                    <div class="panel-collapse collapse show" id="photoPanel">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xl-12 text-center">
                                                    @php
                                                        $coverPhotoError = (isset($errors) && $errors->has('cover_photo')) ? ' is-invalid' : '';
                                                    @endphp
                                                    <div class="photo-field">
                                                        <div class="file-loading">
                                                            <input id="logoField" name="cover_photo" type="file"
                                                                   class="file {{ $coverPhotoError }}">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- company images --}}
                                <div class="card card-default mt-2">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            <a href="#photoPanel" data-bs-toggle="collapse"
                                               data-parent="#accordion">{{ t('Company Images') }}</a>
                                        </h4>
                                    </div>

                                    <div class="panel-collapse collapse show" id="photoPanel">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xl-12 text-center">
                                                    @php
                                                        $imagesError = (isset($errors) && $errors->has('company_images')) ? ' is-invalid' : '';
                                                    @endphp
                                                    <div class="photo-field">
                                                        {{--<div class="file-loading">
                                                            <input id="logoField" name="company_images" type="file" multiple
                                                                   class="file {{ $imagesError }}">
                                                        </div>--}}
                                                        <input name="company_images[]"
                                                               type="file"
                                                               class="file{{ $imagesError }}"
                                                               id="company_images"
                                                               multiple>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- company videos --}}
                                <div class="card card-default mt-2">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            <a href="#photoPanel" data-bs-toggle="collapse"
                                               data-parent="#accordion">{{ t('Company Videos') }}</a>
                                        </h4>
                                    </div>

                                    <div class="panel-collapse collapse show" id="photoPanel">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xl-12 text-center">
                                                    @php
                                                        $videosError = (isset($errors) && $errors->has('company_videos')) ? ' is-invalid' : '';
                                                    @endphp
                                                    <div class="photo-field">
                                                        <input name="company_videos[]"
                                                               type="file"
                                                               class="file{{ $videosError }}"
                                                               id="company_videos"
                                                               multiple>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- button --}}
                                <div class="row mt-2 justify-content-end">
                                    <div>
                                        <button type="submit"
                                                class="btn btn-primary">{{ t('Save') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>


                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('after_styles')
    <link href="{{ url('assets/plugins/bootstrap-fileinput/css/fileinput.min.css') }}" rel="stylesheet">
    @if (config('lang.direction') == 'rtl')
        <link href="{{ url('assets/plugins/bootstrap-fileinput/css/fileinput-rtl.min.css') }}" rel="stylesheet">
    @endif
    @if (str_starts_with($fiTheme, 'explorer'))
        <link href="{{ url('assets/plugins/bootstrap-fileinput/themes/' . $fiTheme . '/theme.min.css') }}"
              rel="stylesheet">
    @endif
    <style>
        .krajee-default.file-preview-frame:hover:not(.file-preview-error) {
            box-shadow: 0 0 5px 0 #666666;
        }

        .file-loading:before {
            content: " {{ t('loading_wd') }}";
        }
    </style>
    <style>
        /* Avatar Upload */
        .photo-field {
            display: inline-block;
            vertical-align: middle;
        }

        .photo-field .krajee-default.file-preview-frame,
        .photo-field .krajee-default.file-preview-frame:hover {
            margin: 0;
            padding: 0;
            border: none;
            box-shadow: none;
            text-align: center;
        }

        .photo-field .file-input {
            display: table-cell;
            min-width: 200px;
        }

        .photo-field .krajee-default.file-preview-frame .kv-file-content {
            width: 150px;
            height: 160px;
        }

        .kv-reqd {
            color: red;
            font-family: monospace;
            font-weight: normal;
        }

        .file-preview {
            padding: 2px;
        }

        .file-drop-zone {
            margin: 2px;
            min-height: 100px;
        }

        .file-drop-zone .file-preview-thumbnails {
            cursor: pointer;
        }

        .krajee-default.file-preview-frame .file-thumbnail-footer {
            height: 30px;
        }

        /* Allow clickable uploaded photos (Not possible) */
        .file-drop-zone {
            padding: 20px;
        }

        .file-drop-zone .kv-file-content {
            padding: 0
        }
    </style>
@endsection

@section('after_scripts')
    <script src="{{ url('assets/plugins/bootstrap-fileinput/js/plugins/sortable.min.js') }}"
            type="text/javascript"></script>
    <script src="{{ url('assets/plugins/bootstrap-fileinput/js/fileinput.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/plugins/bootstrap-fileinput/themes/' . $fiTheme . '/theme.min.js') }}"
            type="text/javascript"></script>
    <script src="{{ url('common/js/fileinput/locales/' . config('app.locale') . '.js') }}"
            type="text/javascript"></script>

@endsection
