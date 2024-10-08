{{-- upload multiple input --}}
<div @include('admin.panel.inc.field_wrapper_attributes') >
    <label class="form-label fw-bolder">{!! $field['label'] !!}</label>
	@include('admin.panel.fields.inc.translatable_icon')

	{{-- Show the file name and a "Clear" button on EDIT form. --}}
	@if (isset($field['value']) && count($field['value']))
    <div class="well well-sm file-preview-container">
    	@foreach($field['value'] as $key => $file_path)
    		<div class="file-preview">
	    		<a target="_blank" href="{{ isset($field['disk'])?asset(\Storage::disk($field['disk'])->url($file_path)):asset($file_path) }}">{{ $file_path }}</a>
		    	<a id="{{ $field['name'] }}_{{ $key }}_clear_button" href="#" class="btn btn-secondary btn-xs float-end file-clear-button" title="Clear file" data-filename="{{ $file_path }}"><i class="fa-solid fa-xmark"></i></a>
		    	<div class="clearfix"></div>
	    	</div>
    	@endforeach
    </div>
    @endif
	{{-- Show the file picker on CREATE form. --}}
	<input name="{{ $field['name'] }}[]" type="hidden" value="">
	<input
        type="file"
        id="{{ $field['name'] }}_file_input"
        name="{{ $field['name'] }}[]"
        value="{{ old($field['name']) ? old($field['name']) : (isset($field['default']) ? $field['default'] : '' ) }}"
        @include('admin.panel.inc.field_attributes')
        multiple
    >

    {{-- HINT --}}
    @if (isset($field['hint']))
        <div class="form-text">{!! $field['hint'] !!}</div>
    @endif
</div>

{{-- FIELD EXTRA JS --}}
{{-- push things in the after_scripts section --}}

    @push('crud_fields_scripts')
        {{-- no scripts --}}
        <script>
	        $(".file-clear-button").click(function(e) {
	        	e.preventDefault();
	        	var container = $(this).parent().parent();
	        	var parent = $(this).parent();
	        	// remove the filename and button
	        	parent.remove();
	        	// if the file container is empty, remove it
	        	if ($.trim(container.html())=='') {
	        		container.remove();
	        	}
	        	$("<input type='hidden' name='clear_{{ $field['name'] }}[]' value='"+$(this).data('filename')+"'>").insertAfter("#{{ $field['name'] }}_file_input");
	        });

	        $("#{{ $field['name'] }}_file_input").change(function() {
	        	console.log($(this).val());
	        	// remove the hidden input, so that the setXAttribute method is no longer triggered
	        	$(this).next("input[type=hidden]").remove();
	        });
        </script>
    @endpush
