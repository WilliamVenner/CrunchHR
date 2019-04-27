<?php
	$unix_timestamp;
	
	switch(gettype($timestamp)) {
		case 'integer':
			$unix_timestamp = $timestamp; break;
		case 'object':
			$unix_timestamp = $timestamp -> timestamp; break;
		case 'string':
			$unix_timestamp = strtotime($timestamp); break;
	}
?>

<span class="timestamp" data-timestamp="{{ $unix_timestamp }}">{{ date('D jS M Y g:i:sa', $unix_timestamp) }}</span>