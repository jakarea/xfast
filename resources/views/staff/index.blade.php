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
	$staffs ??= []; 
	$pagePath ??= null;
	
	$pageTitles = [
		'list' => [
			'icon'  => 'fa-solid fa-bullhorn',
			'title' => t('staff_list'),
		],
		'archived' => [
			'icon'  => 'fa-solid fa-calendar-xmark',
			'title' => t('staff_panel'),
		],
		'favourite' => [
			'icon'  => 'fa-solid fa-bookmark',
			'title' => t('favourite_listings'),
		],
		'pending-approval' => [
			'icon'  => 'fa-solid fa-hourglass-half',
			'title' => t('pending_approval'),
		],
	];
@endphp

@section('content')
	@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
	<div class="main-container">
		<div class="container">
			<div class="row">
				@if (session()->has('flash_notification'))
					<div class="col-xl-12">
						<div class="row">
							<div class="col-xl-12">
								@include('flash::message')
							</div>
						</div>
					</div>
				@endif
				
				<div class="col-md-3 page-sidebar">
					@includeFirst([config('larapen.core.customizedViewPath') . 'account.inc.sidebar', 'account.inc.sidebar'])
				</div>

				<div class="col-md-9 page-content">
					<div class="inner-box">
						<h2 class="title-2">
							<i class="{{ $pageTitles[$pagePath]['icon'] ?? 'fa-solid fa-bullhorn' }}"></i>
							{{ t('staff_list') }}
						</h2>
						
						<div class="table-responsive">
							<form name="listForm" method="POST" action="{{ url('account/posts/' . $pagePath . '/delete') }}">
								{!! csrf_field() !!}
								<div class="table-action">
									<div class="btn-group hidden-sm" role="group">
										<button type="button" class="btn btn-sm btn-default pb-0">
											<input type="checkbox" id="checkAll" class="from-check-all">
										</button>
										<button type="button" class="btn btn-sm btn-default from-check-all">
											{{ t('Select') }}: {{ t('All') }}
										</button>
									</div>
									
									<button type="submit" class="btn btn-sm btn-default confirm-simple-action">
										<i class="fa-regular fa-trash-can"></i> {{ t('Delete') }}
									</button>
									
									<div class="table-search float-end col-sm-7">
										<div class="row">
											<label class="col-5 form-label text-end">{{ t('search') }} <br>
												<a title="clear filter" class="clear-filter" href="#clear">[{{ t('clear') }}]</a>
											</label>
											<div class="col-7 searchpan px-3">
												<input type="text" class="form-control" id="filter">
											</div>
										</div>
									</div>
								</div>
								
								<table id="addManageTable"
									   class="table table-striped table-bordered add-manage-table table demo"
									   data-filter="#filter"
									   data-filter-text-only="true"
								>
									<thead>
									<tr>
										<th data-type="numeric" data-sort-initial="true"></th>
										<th>{{ t('Name') }}</th>
										<th data-sort-ignore="true">{{ t('Email') }}</th>
										<th data-type="numeric">
											{{ t('Country Code') }}
										</th>
										<th>{{ t('Option') }}</th>
									</tr>
									</thead>
									<tbody>
									
									@if (!empty($staffs) > 0)
										@foreach($staffs as $key => $staff)
											<tr>
												<td style="width:2%" class="add-img-selector">
													<div class="checkbox">
														<label><input type="checkbox" name="entries[]" value="{{ data_get($staff, 'id') }}"></label>
													</div>
												</td>
												<td style="width:20%" class="add-img-td">
													<a href="{{ \App\Helpers\UrlGen::post($staff) }}"> 
														{{ $staff->name }}
													</a>
												</td>
												<td style="width:32%" class="items-details-td">
													<p>{{ $staff->email }}</p> 
												</td>
												<td style="width:16%" class="items-details-td">
													<p>{{ $staff->country_code }}</p> 
												</td> 
												<td style="width:10%" class="action-td">
													@if (hasOwnerPermission(auth()->id(),'staff_info_manage')) 
													<div>  
														<a class="btn btn-info btn-sm confirm-simple-action"
															href="{{ url('staff-management/edit/'.$staff->id) }}"
														>
															<i class="fa-regular fa-eye"></i> {{ t('Edit') }}
														</a> 
														<a class="btn btn-danger btn-sm confirm-simple-action"
															href="{{ route('destroy.staff',$staff->id) }}"
														>
															<i class="fa-regular fa-trash-can"></i> {{ t('Delete') }}
														</a> 
													</div>
													@endif
												</td>
											</tr>
										@endforeach
									@endif
									</tbody>
								</table>
							</form>
						</div>
						
						<nav>
							@include('vendor.pagination.api.bootstrap-4')
						</nav>
						
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('after_styles')
	<style>
		.action-td p {
			margin-bottom: 5px;
		}
	</style>
@endsection

@section('after_scripts')
	<script src="{{ url('assets/js/footable.js?v=2-0-1') }}" type="text/javascript"></script>
	<script src="{{ url('assets/js/footable.filter.js?v=2-0-1') }}" type="text/javascript"></script>
	<script type="text/javascript">
		$(function () {
			$('#addManageTable').footable().bind('footable_filtering', function (e) {
				let selected = $('.filter-status').find(':selected').text();
				if (selected && selected.length > 0) {
					e.filter += (e.filter && e.filter.length > 0) ? ' ' + selected : selected;
					e.clear = !e.filter;
				}
			});

			$('.clear-filter').click(function (e) {
				e.preventDefault();
				$('.filter-status').val('');
				$('table.demo').trigger('footable_clear_filter');
			});

			$('.from-check-all').click(function () {
				checkAll(this);
			});
		});
	</script>
	{{-- include custom script for listings table [select all checkbox]  --}}
	<script>
		function checkAll(bx) {
			if (bx.type !== 'checkbox') {
				bx = document.getElementById('checkAll');
				bx.checked = !bx.checked;
			}
			
			var chkinput = document.getElementsByTagName('input');
			for (var i = 0; i < chkinput.length; i++) {
				if (chkinput[i].type === 'checkbox') {
					chkinput[i].checked = bx.checked;
				}
			}
		}
	</script>
@endsection
