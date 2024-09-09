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
                <div class="col-md-3 page-sidebar" id="sidebarSection">
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
                                                            <input id="coverPhotoField" name="cover_photo" type="file"
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
                                                    <!-- File count and error message display -->
                                                    <div id="fileCountDisplay" class="mt-2"></div>
                                                    <div id="imageUploadError" class="mt-2"></div>
                                                    @php
                                                        $imagesError = (isset($errors) && $errors->has('company_images')) ? ' is-invalid' : '';
                                                    @endphp
                                                    <div class="photo-field">
                                                        <input name="company_images[]"
                                                               type="file"
                                                               class="file{{ $imagesError }}"
                                                               id="company_images_field"
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
                                                    <!-- File count and error message display -->
                                                    <div id="videoCountDisplay" class="mt-2"></div>
                                                    <div id="videoUploadError" class="mt-2"></div>
                                                    @php
                                                        $videosError = (isset($errors) && $errors->has('company_videos')) ? ' is-invalid' : '';
                                                    @endphp
                                                    <div class="photo-field">
                                                        <input name="company_videos[]"
                                                               type="file"
                                                               class="file{{ $videosError }}"
                                                               id="company_videos_field"
                                                               multiple>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- social media links --}}
                                <div class="card card-default mt-2">
                                    <div class="card-header">
                                        <h4 class="card-title">
                                            <a href="#photoPanel" data-bs-toggle="collapse"
                                               data-parent="#accordion">{{ t('social_media_links') }}</a>
                                        </h4>
                                    </div>

                                    <div class="panel-collapse collapse show" id="photoPanel">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-xl-12 text-center">
                                                    @php
                                                        $videosError = (isset($errors) && $errors->has('company_videos')) ? ' is-invalid' : '';
                                                    @endphp
                                                    <div id="social-media-links-wrapper">
                                                        <!-- Dynamically added social media link inputs will appear here -->
                                                        @if(isset($business->social_media_links) && is_array($business->social_media_links))
                                                            @foreach($business->social_media_links as $index => $link)
                                                                <div class="input-group mb-2" data-index="{{ $index }}">
                                                                    <input type="text" name="social_media_links[]"
                                                                           class="form-control" value="{{ $link }}"
                                                                           placeholder="Enter social media link">
                                                                    <button type="button"
                                                                            class="btn btn-danger remove-social-link">{{ t('remove') }}</button>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="input-group mb-2">
                                                                <input type="text" name="social_media_links[]"
                                                                       class="form-control"
                                                                       placeholder="Enter social media link">
                                                                <button type="button"
                                                                        class="btn btn-danger remove-social-link">{{ t('remove') }}</button>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Button to add new link -->
                                                    <button type="button" class="btn btn-primary mt-2"
                                                            id="add-social-link">{{ t('add_link') }}</button>

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

    <script>
        /**social links start*/
        $(document).ready(function () {
            // Function to add new social media link input field
            $('#add-social-link').on('click', function () {
                let newIndex = $('#social-media-links-wrapper .input-group').length; // Get current count
                let newLinkField = `
            <div class="input-group mb-2" data-index="${newIndex}">
                <input type="text" name="social_media_links[]" class="form-control" placeholder="Enter social media link">
                <button type="button" class="btn btn-danger remove-social-link">Remove</button>
            </div>
        `;
                $('#social-media-links-wrapper').append(newLinkField);
            });

            // Function to remove a social media link input field
            $(document).on('click', '.remove-social-link', function () {
                $(this).closest('.input-group').remove();
            });
        });
        /**social links end*/

        /**for logo start*/
        let options = {};
        options.theme = '{{ $fiTheme }}';
        options.language = '{{ config('app.locale') }}';
        options.rtl = {{ (config('lang.direction') == 'rtl') ? 'true' : 'false' }};
        options.overwriteInitial = true;
        options.showCaption = false;
        options.showPreview = true;
        options.allowedFileExtensions = {!! getUploadFileTypes('image', true) !!};
        options.showClose = false;
        options.showBrowse = true;
        options.browseClass = 'btn btn-primary';
        options.minFileSize = {{ (int)config('settings.upload.min_image_size', 0) }};
        options.maxFileSize = {{ (int)config('settings.upload.max_image_size', 1000) }};
        options.uploadAsync = false;
        options.browseOnZoneClick = true;
        options.minFileCount = 0;
        options.maxFileCount = 1;
        options.validateInitialCount = true;
        options.initialPreview = [];
        options.initialPreviewAsData = true;
        options.initialPreviewFileType = 'image';
        options.initialPreviewConfig = [];
        options.fileActionSettings = {
            showDrag: false,
            showRemove: true,
            removeClass: 'btn btn-outline-danger btn-sm',
            showZoom: true,
            zoomClass: 'btn btn-outline-secondary btn-sm'
        };
        options.elErrorContainer = '#avatarUploadError';
        options.msgErrorClass = 'alert alert-block alert-danger';
        options.layoutTemplates = {
            main2: '{preview}\n<div class="kv-upload-progress hide"></div>\n{browse}',
            footer: '<div class="file-thumbnail-footer pt-2">\n{actions}\n</div>',
            actions: '<div class="file-actions">\n'
                + '<div class="file-footer-buttons">\n {zoom}</div>\n'
                + '<div class="clearfix"></div>\n'
                + '</div>'
        };

        @if (!empty($business->logo))
                @php
                    try {
                        $fileSize = (isset($disk) && $disk->exists($business->logo)) ? (int)$disk->size($business->logo) : 0;
                    } catch (\Throwable $e) {
                        $fileSize = 0;
                    }
                @endphp
            options.initialPreview[0] = "{{ asset('storage/' . $business->logo) }}";
        options.initialPreviewConfig[0] = {};
        options.initialPreviewConfig[0].key = {{ (int)$business->id }};
        options.initialPreviewConfig[0].caption = '{{ basename($business->logo) }}';
        options.initialPreviewConfig[0].size = {{ $fileSize }};
        @endif


        let photoFieldEl = $('#logoField');
        photoFieldEl.fileinput(options);
        /**for logo end*/

        /**for cover Photo start*/
        let coverPhotoOptions = {};
        coverPhotoOptions.theme = '{{ $fiTheme }}';
        coverPhotoOptions.language = '{{ config('app.locale') }}';
        coverPhotoOptions.rtl = {{ (config('lang.direction') == 'rtl') ? 'true' : 'false' }};
        coverPhotoOptions.overwriteInitial = true;
        coverPhotoOptions.showCaption = false;
        coverPhotoOptions.showPreview = true;
        coverPhotoOptions.allowedFileExtensions = {!! getUploadFileTypes('image', true) !!};
        coverPhotoOptions.showClose = false;
        coverPhotoOptions.showBrowse = true;
        coverPhotoOptions.browseClass = 'btn btn-primary';
        coverPhotoOptions.minFileSize = {{ (int)config('settings.upload.min_image_size', 0) }};
        coverPhotoOptions.maxFileSize = {{ (int)config('settings.upload.max_image_size', 1000) }};
        coverPhotoOptions.uploadAsync = false;
        coverPhotoOptions.browseOnZoneClick = true;
        coverPhotoOptions.minFileCount = 0;
        coverPhotoOptions.maxFileCount = 1;
        coverPhotoOptions.validateInitialCount = true;
        coverPhotoOptions.initialPreview = [];
        coverPhotoOptions.initialPreviewAsData = true;
        coverPhotoOptions.initialPreviewFileType = 'image';
        coverPhotoOptions.initialPreviewConfig = [];
        coverPhotoOptions.fileActionSettings = {
            showDrag: false,
            showRemove: true,
            removeClass: 'btn btn-outline-danger btn-sm',
            showZoom: true,
            zoomClass: 'btn btn-outline-secondary btn-sm'
        };
        coverPhotoOptions.elErrorContainer = '#avatarUploadError';
        coverPhotoOptions.msgErrorClass = 'alert alert-block alert-danger';
        coverPhotoOptions.layoutTemplates = {
            main2: '{preview}\n<div class="kv-upload-progress hide"></div>\n{browse}',
            footer: '<div class="file-thumbnail-footer pt-2">\n{actions}\n</div>',
            actions: '<div class="file-actions">\n'
                + '<div class="file-footer-buttons">\n {zoom}</div>\n'
                + '<div class="clearfix"></div>\n'
                + '</div>'
        };

        @if (!empty($business->cover_photo))
                @php
                    try {
                        $fileSize = (isset($disk) && $disk->exists($business->cover_photo)) ? (int)$disk->size($business->cover_photo) : 0;
                    } catch (\Throwable $e) {
                        $fileSize = 0;
                    }
                @endphp
            coverPhotoOptions.initialPreview[0] = "{{ asset('storage/' . $business->cover_photo) }}";
        coverPhotoOptions.initialPreviewConfig[0] = {};
        coverPhotoOptions.initialPreviewConfig[0].key = {{ (int)$business->id }};
        coverPhotoOptions.initialPreviewConfig[0].caption = '{{ basename($business->cover_photo) }}';
        coverPhotoOptions.initialPreviewConfig[0].size = {{ $fileSize }};
        coverPhotoOptions.initialPreviewConfig[0].extra = options.uploadExtraData;
        @endif


        let coverPhotoField = $('#coverPhotoField');
        coverPhotoField.fileinput(coverPhotoOptions);

        /* Delete picture */

        /**for cover Photo end*/


        /** For company images start */
        let companyImagesOptions = {};
        companyImagesOptions.theme = '{{ $fiTheme }}';
        companyImagesOptions.language = '{{ config('app.locale') }}';
        companyImagesOptions.rtl = {{ (config('lang.direction') == 'rtl') ? 'true' : 'false' }};
        companyImagesOptions.overwriteInitial = false;
        companyImagesOptions.showCaption = false;
        companyImagesOptions.showPreview = true;
        companyImagesOptions.allowedFileExtensions = {!! getUploadFileTypes('image', true) !!};
        companyImagesOptions.showClose = false;
        companyImagesOptions.showBrowse = true;
        companyImagesOptions.browseClass = 'btn btn-primary';
        companyImagesOptions.minFileSize = {{ (int)config('settings.upload.min_image_size', 0) }};
        companyImagesOptions.maxFileSize = {{ (int)config('settings.upload.max_image_size', 1000) }};
        companyImagesOptions.uploadAsync = false;
        companyImagesOptions.browseOnZoneClick = true;
        companyImagesOptions.minFileCount = 0;
        companyImagesOptions.maxFileCount = 4;
        companyImagesOptions.validateInitialCount = true;
        companyImagesOptions.initialPreview = [];
        companyImagesOptions.initialPreviewAsData = true;
        companyImagesOptions.initialPreviewFileType = 'image';
        companyImagesOptions.initialPreviewConfig = [];
        companyImagesOptions.fileActionSettings = {
            showDrag: false,
            showRemove: true,
            removeClass: 'btn btn-outline-danger btn-sm',
            showZoom: true,
            zoomClass: 'btn btn-outline-secondary btn-sm',
        };
        companyImagesOptions.elErrorContainer = '#imageUploadError';
        companyImagesOptions.msgErrorClass = 'alert alert-block alert-danger';
        companyImagesOptions.layoutTemplates = {
            main2: '{preview}\n<div class="kv-upload-progress hide"></div>\n{browse}',
            footer: '<div class="file-thumbnail-footer pt-2">\n{actions}\n</div>',
            actions: '<div class="file-actions">\n'
                + '<div class="file-footer-buttons">\n{delete} {zoom}</div>\n'
                + '<div class="clearfix"></div>\n'
                + '</div>'
        };
        // Custom message for max file count error
        companyImagesOptions.msgFilesTooMany = 'You can upload a maximum of {m} images. But you have selected {n} images.';

        // Populate the initialPreview and initialPreviewConfig with the existing images
        @foreach($business->company_images as $index => $image)
        companyImagesOptions.initialPreview.push("{{ asset('storage/' . $image) }}");
        companyImagesOptions.initialPreviewConfig.push({
            caption: '{{ basename($image) }}',
            size: 1024,  // Example size in KB
            key: {{ $index + 1 }},
            url: "{{ route('business.removeImage', [$business->id, $index]) }}",
            extra: {_token: "{{ csrf_token() }}"},  // Add CSRF token for delete requests
        });
        @endforeach

        let companyImages = $('#company_images_field');
        companyImages.fileinput(companyImagesOptions);

        // Update file count display
        function updateCompanyImagesCountDisplay() {
            let maxCount = companyImagesOptions.maxFileCount;
            let currentCount = companyImages.fileinput('getFilesCount');
            let fileCountDisplay = $('#fileCountDisplay');
            let errorDisplay = $('#imageUploadError');

            fileCountDisplay.html(`Selected Company Images: ${currentCount}/${maxCount}`);

            if (currentCount > maxCount) {
                errorDisplay.html(`<div class="${companyImagesOptions.msgErrorClass}">${companyImagesOptions.msgFilesTooMany.replace('{m}', maxCount).replace('{n}', currentCount)}</div>`);
                companyImages.fileinput('clear');
            } else {
                errorDisplay.html('');
            }
        }

        // Listen for file selection and update UI
        companyImages.on('filebatchselected', function (event, data) {
            updateCompanyImagesCountDisplay();
        });
        updateCompanyImagesCountDisplay();

        /* Track and Remove Deleted Images from the UI */

        companyImages.on('filepredelete', function (event, key, jqXHR, data) {
            event.preventDefault();  // Prevent default action for now

            // Fetch and remove the preview element using a more appropriate selector
            let previewElement = $(`#company_images_field .kv-file-remove[data-key="${key}"]`).closest('.kv-preview-thumb');

            if (previewElement) {
                previewElement.fadeOut('slow', function () {  // Smooth removal animation
                    previewElement.remove();  // Remove the image's preview element
                });
                console.log('Deleted file with key: ' + key);  // Log the deleted image's key
            }

        });
        /** For company images end */


        /** For company videos start */
        let CompanyVideosOptions = {};
        CompanyVideosOptions.theme = '{{ $fiTheme }}';
        CompanyVideosOptions.language = '{{ config('app.locale') }}';
        CompanyVideosOptions.rtl = {{ (config('lang.direction') == 'rtl') ? 'true' : 'false' }};
        CompanyVideosOptions.overwriteInitial = false; // Allow multiple previews
        CompanyVideosOptions.showCaption = false;
        CompanyVideosOptions.showPreview = true;
        CompanyVideosOptions.allowedFileExtensions = ['mp4', 'avi', 'mov', 'wmv']; // Allowed video extensions
        CompanyVideosOptions.showClose = false;
        CompanyVideosOptions.showBrowse = true;
        CompanyVideosOptions.browseClass = 'btn btn-primary';
        CompanyVideosOptions.minFileSize = {{ (int)config('settings.upload.min_video_size', 0) }};
        CompanyVideosOptions.maxFileSize = {{ (int)config('settings.upload.max_video_size', 10000) }}; // Adjust as per your max file size
        CompanyVideosOptions.uploadAsync = false;
        CompanyVideosOptions.browseOnZoneClick = true;
        CompanyVideosOptions.minFileCount = 0;
        CompanyVideosOptions.maxFileCount = 2; // Maximum 5 videos
        CompanyVideosOptions.validateInitialCount = true;
        CompanyVideosOptions.initialPreview = [];
        CompanyVideosOptions.initialPreviewAsData = true; // Treat initial preview data as video
        CompanyVideosOptions.initialPreviewFileType = 'video'; // Set to video
        CompanyVideosOptions.initialPreviewConfig = [];
        CompanyVideosOptions.fileActionSettings = {
            showDrag: false,
            showRemove: true,
            removeClass: 'btn btn-outline-danger btn-sm',
            showZoom: true,
            zoomClass: 'btn btn-outline-secondary btn-sm'
        };
        CompanyVideosOptions.elErrorContainer = '#avatarUploadError';
        CompanyVideosOptions.msgErrorClass = 'alert alert-block alert-danger';
        CompanyVideosOptions.layoutTemplates = {
            main2: '{preview}\n<div class="kv-upload-progress hide"></div>\n{browse}',
            footer: '<div class="file-thumbnail-footer pt-2">\n{actions}\n</div>',
            actions: '<div class="file-actions">\n'
                + '<div class="file-footer-buttons">\n{delete} {zoom}</div>\n'
                + '<div class="clearfix"></div>\n'
                + '</div>'
        };

        // Custom message for max file count error
        CompanyVideosOptions.msgFilesTooMany = 'You can upload a maximum of {m} videos. But you have selected {n} videos.';


        // If editing, set up initial preview for the existing videos
        @foreach($business->company_videos as $index => $video)
        CompanyVideosOptions.initialPreview.push("{{ asset('storage/' . $video) }}");
        CompanyVideosOptions.initialPreviewConfig.push({
            caption: '{{ basename($video) }}',
            type: 'video', // Specifies that this is a video file
            filetype: 'video/mp4', // Adjust if necessary
            size: 1024,  // Example size in KB (you may get actual size from the backend)
            key: {{ $index + 1 }},
            url: "{{ route('business.removeVideo', [$business->id, $index]) }}",
            extra: {_token: "{{ csrf_token() }}"}  // Add CSRF token for delete requests
        });
        @endforeach

        let companyVideos = $('#company_videos_field');
        companyVideos.fileinput(CompanyVideosOptions);

        // Update file count display
        function updateVideoCountDisplay() {
            let maxCount = CompanyVideosOptions.maxFileCount;
            let currentCount = companyVideos.fileinput('getFilesCount');
            let fileCountDisplay = $('#videoCountDisplay');
            let errorDisplay = $('#videoUploadError');

            fileCountDisplay.html(`Selected Company Videos: ${currentCount}/${maxCount}`);

            if (currentCount > maxCount) {
                errorDisplay.html(`<div class="${CompanyVideosOptions.msgErrorClass}">${CompanyVideosOptions.msgFilesTooMany.replace('{m}', maxCount).replace('{n}', currentCount)}</div>`);
                companyVideos.fileinput('clear');
            } else {
                errorDisplay.html('');
            }
        }

        // Listen for file selection and update UI
        companyVideos.on('filebatchselected', function (event, data) {
            updateVideoCountDisplay();
        });
        updateVideoCountDisplay();

        /* Track and Remove Deleted Images from the UI */

        companyVideos.on('filepredelete', function (event, key, jqXHR, data) {
            event.preventDefault();  // Prevent default action for now

            // Fetch and remove the preview element using a more appropriate selector
            let previewElement = $(`#company_videos_field .kv-file-remove[data-key="${key}"]`).closest('.kv-preview-thumb');

            if (previewElement) {
                previewElement.fadeOut('slow', function () {  // Smooth removal animation
                    previewElement.remove();  // Remove the image's preview element
                });
                console.log('Deleted file with key: ' + key);  // Log the deleted image's key
            }

        });

        /** For company videos end */
    </script>
@endsection
