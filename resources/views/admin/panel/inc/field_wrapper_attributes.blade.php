@php
	$fieldName = str_replace('[]', '', $field['name']);
	$fieldName = str_replace('][', '.', $fieldName);
	$fieldName = str_replace('[', '.', $fieldName);
	$fieldName = str_replace(']', '', $fieldName);
	
	$required = (
		isset($field['rules'])
		&& isset($field['rules'][$fieldName])
		&& in_array('required', explode('|', $field['rules'][$fieldName]))
	) ? true : '';
	
	// Get Attributes Output
	$attr = '';
	if (isset($field['wrapperAttributes'])) {
		// wrapperAttributes option is defined
		foreach ($field['wrapperAttributes'] as $attribute => $value) {
			if (is_string($attribute)) {
				if ($attribute == 'class') {
					if (isset($field['type'])) {
						$attr .= $attribute . '="mb-3 ' . $value;
						if ($field['type'] == 'image') {
							$attr .= ' image';
						}
						if ($field['type'] == 'color_picker') {
							$attr .= ' coloris square';
						}
						$attr .= '"';
					} else {
						$attr .= $attribute . '="mb-3 ' . $value . '"';
					}
				} else {
					$attr .= $attribute . '="' . $value . '"';
				}
			}
		}
		
		// class attribute is not set in wrapperAttributes
		if (!isset($field['wrapperAttributes']['class'])) {
			// Add the class attribute (with some default values) related to the 'type' of field
			if (isset($field['type'])) {
				$attr .= 'class="mb-3 col-md-12';
				if ($field['type'] == 'image') {
					$attr .= ' image';
				}
				if ($field['type'] == 'color_picker') {
					$attr .= ' coloris square';
				}
				$attr .= '"';
			} else {
				$attr .= 'class="mb-3 col-md-12"';
			}
		}
		
	} else {
		// wrapperAttributes option is not defined
		// Add the class attribute (with some default values) related to the 'type' of field
		if (isset($field['type'])) {
			$attr .= 'class="mb-3 col-md-12';
			if ($field['type'] == 'image') {
				$attr .= ' image';
			}
			if ($field['type'] == 'color_picker') {
				$attr .= ' coloris square';
			}
			$attr .= '"';
		} else {
			$attr .= 'class="mb-3 col-md-12"';
		}
	}
@endphp
{!! $attr !!}
