{{-- select2 from ajax multiple --}}
@php
	$connected_entity = new $field['model'];
	$connected_entity_key_name = $connected_entity->getKeyName();
	$old_value = old($field['name']) ? old($field['name']) : (isset($field['value']) ? $field['value'] : (isset($field['default']) ? $field['default'] : false ));
@endphp

<div @include('admin.panel.inc.field_wrapper_attributes') >
	<label class="form-label fw-bolder">{!! $field['label'] !!}</label>
	@include('admin.panel.fields.inc.translatable_icon')
	<select
			name="{{ $field['name'] }}[]"
			style="width: 100%"
			id="select2_ajax_multiple_{{ $field['name'] }}"
			@include('admin.panel.inc.field_attributes', ['default_class' =>  'form-control'])
			multiple>
		
		@if ($old_value)
			@foreach ($old_value as $result_key)
				@php
					$item = $connected_entity->find($result_key);
				@endphp
				@if ($item)
					<option value="{{ $item->getKey() }}" selected>
						{{ $item->{$field['attribute']} }}
					</option>
				@endif
			@endforeach
		@endif
	</select>
	
	{{-- HINT --}}
	@if (isset($field['hint']))
		<div class="form-text">{!! $field['hint'] !!}</div>
	@endif
</div>


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($xPanel->checkIfFieldIsFirstOfItsType($field, $fields))
	
	{{-- FIELD CSS - will be loaded in the after_styles section --}}
	@push('crud_fields_styles')
	{{-- include select2 css--}}
	<link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ asset('assets/plugins/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
	@endpush
	
	{{-- FIELD JS - will be loaded in the after_scripts section --}}
	@push('crud_fields_scripts')
	{{-- include select2 js--}}
	<script src="{{ asset('assets/plugins/select2/js/select2.js') }}"></script>
	@endpush

@endif

{{-- include field specific select2 js--}}
@push('crud_fields_scripts')
<script>
	jQuery(document).ready(function($) {
		// trigger select2 for each untriggered select2 box
		$("#select2_ajax_multiple_{{ $field['name'] }}").each(function (i, obj) {
			if (!$(obj).hasClass("select2-hidden-accessible"))
			{
				$(obj).select2({
					theme: 'bootstrap',
					multiple: true,
					placeholder: "{{ $field['placeholder'] }}",
					minimumInputLength: "{{ $field['minimum_input_length'] }}",
					ajax: {
						url: "{{ $field['data_source'] }}",
						dataType: 'json',
						quietMillis: 250,
						data: function (params) {
							return {
								q: params.term, // search term
								page: params.page
							};
						},
						processResults: function (data, params) {
							params.page = params.page || 1;
							
							return {
								results: $.map(data.data, function (item) {
									return {
										text: item["{{$field['attribute']}}"],
										id: item["{{ $connected_entity_key_name }}"]
									}
								}),
								more: data.current_page < data.last_page
							};
						},
						cache: true
					},
				});
			}
		});
	});
</script>
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
