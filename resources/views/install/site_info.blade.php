@extends('install.layouts.master')

@section('title', trans('messages.configuration'))

@section('content')
	
	<form action="{{ $installUrl . '/site_info' }}" method="POST">
		{!! csrf_field() !!}
		
		<h3 class="title-3"><i class="fa-solid fa-globe"></i> {{ trans('messages.general') }}</h3>
		<div class="row">
			<div class="col-md-6">
				@include('install.helpers.form_control', [
					'type'  => 'text',
					'name'  => 'site_name',
					'value' => $siteInfo['site_name'] ?? '',
					'rules' => ['site_name' => 'required']
				])
			</div>
			<div class="col-md-6">
				@include('install.helpers.form_control', [
					'type'  => 'text',
					'name'  => 'site_slogan',
					'value' => $siteInfo['site_slogan'] ?? '',
					'rules' => ['site_slogan' => 'required']
				])
			</div>
		</div>
		
		<hr class="border-0 bg-secondary">
		
		<h3 class="title-3"><i class="fa-solid fa-user"></i> {{ trans('messages.admin_info') }}</h3>
		<div class="row">
			<div class="col-md-6">
				@include('install.helpers.form_control', [
					'type'  => 'text',
					'name'  => 'name',
					'value' => $siteInfo['name'] ?? '',
					'rules' => $rules
				])
			</div>
			<div class="col-md-6">
				@include('install.helpers.form_control', [
					'type'  => 'text',
					'name'  => 'purchase_code',
					'value' => $siteInfo['purchase_code'] ?? '',
					'hint'  => trans('admin.find_my_purchase_code'),
					'rules' => $rules
				])
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				@include('install.helpers.form_control', [
					'type'  => 'text',
					'name'  => 'email',
					'value' => $siteInfo['email'] ?? '',
					'rules' => $rules
				])
			</div>
			<div class="col-md-6">
				@include('install.helpers.form_control', [
					'type'  => 'text',
					'name'  => 'password',
					'value' => $siteInfo['password'] ?? '',
					'rules' => $rules
				])
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				@include('install.helpers.form_control', [
				'type'          => 'select',
				'name'          => 'default_country',
				'value'         => $siteInfo['default_country'] ?? \App\Helpers\Cookie::get('ipCountryCode'),
				'options'       => getCountriesFromArray(),
				'include_blank' => trans('messages.choose'),
				'rules'         => $rules
				])
			</div>
		</div>
		
		<hr class="border-0 bg-secondary">
		
		<h3 class="title-3"><i class="fa-solid fa-envelope"></i> {{ trans('messages.system_email_configuration') }}</h3>
		<div class="row row-cols-2">
			<div class="col">
				@include('install.helpers.form_control', [
					'type'    => 'select',
					'name'    => 'driver',
					'label'   => trans('messages.mail_driver'),
					'value'   => $siteInfo['driver'] ?? '',
					'options' => [
						['value' => 'sendmail', 'text' => trans('messages.sendmail')],
						['value' => 'smtp', 'text' => trans('messages.smtp')],
						['value' => 'mailgun', 'text' => trans('messages.mailgun')],
						['value' => 'postmark', 'text' => trans('messages.postmark')],
						['value' => 'ses', 'text' => trans('messages.ses')],
						['value' => 'sparkpost', 'text' => trans('messages.sparkpost')],
						['value' => 'mailersend', 'text' => trans('messages.mailersend')],
					],
					'rules' => $rules
				])
			</div>
			<div class="col">
				@include('install.helpers.form_control', [
					'type'    => 'checkbox',
					'name'    => 'validate_driver',
					'label'   => trans('messages.validate_driver_label'),
					'options' => ['0', '1'],
					'value'   => $siteInfo['validate_driver'] ?? '0',
					'hint'    => trans('admin.validate_mail_driver_hint'),
					'rules'   => $rules
				])
			</div>
		</div>
		
		@include('install.site_info.sendmail')
		@include('install.site_info.smtp')
		@include('install.site_info.mailgun')
		@include('install.site_info.postmark')
		@include('install.site_info.ses')
		@include('install.site_info.sparkpost')
		@include('install.site_info.mailersend')
		
		<hr class="border-0 bg-secondary">
		
		<div class="text-end">
			<button type="submit" class="btn btn-primary" data-wait="{{ trans('messages.button_processing') }}">
				{!! trans('messages.next') !!} <i class="fa-solid fa-chevron-right position-right"></i>
			</button>
		</div>
	
	</form>

@endsection

@section('after_scripts')
	<script type="text/javascript" src="{{ url()->asset('assets/plugins/forms/styling/uniform.min.js') }}"></script>
	<script>
		function toggleMailer(driverEl, validateDriverEl) {
			if (driverEl.length <= 0 || validateDriverEl.length <= 0) {
				return;
			}
			
			let selectedDriver = driverEl.val();
			let isDriverValidationEnabled = (validateDriverEl.prop(':checked') || validateDriverEl.is(':checked'));
			
			/* Hide all drivers fields */
			let availableDrivers = ['sendmail', 'smtp', 'mailgun', 'postmark', 'ses', 'sparkpost', 'mailersend'];
			availableDrivers.forEach((driver) => {
				let driverSelector = '.' + driver + '-box';
				let driverSelectorEl = $(driverSelector);
				if (driverSelectorEl.length > 0) {
					driverSelectorEl.hide();
				}
			});
			
			/* Show the selected driver fields */
			let selectedDriverSelector = '.' + selectedDriver + '-box';
			let selectedDriverSelectorEl = $(selectedDriverSelector);
			if (selectedDriverSelectorEl.length > 0) {
				if (selectedDriver === 'sendmail') {
					/* Show the 'sendmail' driver fields only when the driver validation is enabled */
					/* That allows to use default sendmail parameters if validation is not required */
					if (isDriverValidationEnabled) {
						selectedDriverSelectorEl.show();
					}
				} else {
					selectedDriverSelectorEl.show();
				}
			}
		}
		
		$(document).ready(function () {
			let driverEl = $('select[name="driver"]');
			let validateDriverEl = $('input[name="validate_driver"]');
			
			toggleMailer(driverEl, validateDriverEl);
			driverEl.change(function () {
				toggleMailer($(this), validateDriverEl);
			});
			validateDriverEl.click(function () {
				toggleMailer(driverEl, $(this));
			});
		});
	</script>
@endsection
