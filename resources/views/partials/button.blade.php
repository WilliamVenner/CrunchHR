@if (isset($link))
	@if (isset($newtab) && $newtab == true)
		<a href="{{ $link }}" target="_blank">
	@else
		<a href="{{ $link }}">
	@endif
@endif

<?php
	$element_name_open = 'div';
	$element_name_close = 'div';
	
	if (isset($submit) && $submit == true) {
		$element_name_open = 'button type="submit"';
		if (isset($disabled) && $disabled == true) {
			$element_name_open .= ' disabled';
		}
		$element_name_close = 'button';
	}
?>

<{!! $element_name_open !!} {!! isset($name) ? 'name="' . htmlentities($name) . '"' : '' !!} {!! isset($id) ? 'id="' . htmlentities($id) . '"' : '' !!} class="btn {{ $color }} {{ isset($small) && $small == true ? 'small' : '' }} {{ isset($disabled) && $disabled == true ? 'disabled' : '' }} {{ isset($class) ? $class : '' }}" {!! isset($style) ? 'style="' . str_replace('"', '\\"', $style) . '"' : '' !!}>
	@if (isset($icon))
		<div class="icon"><i class="{{ $icon }}"></i></div>
		<div class="text" style="padding-left:{{ isset($small) && $small == true ? '25px' : '30px' }}">{{ $text }}</div>
	@else
		<div class="text">{{ $text }}</div>
	@endif
</{!! $element_name_close !!}>

@if (isset($link))
	</a>
@endif