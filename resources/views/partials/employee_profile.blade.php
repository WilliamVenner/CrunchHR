<?php
	$ProfileEmployee;
	if (isset($employee_id))
		$ProfileEmployee = \App\Models\Employee::where('id', $employee_id)->first();
	else
		$ProfileEmployee = $employee;
?>

@if (isset($link) && $link == true)
	<a href="/employees/{{ $ProfileEmployee -> id }}">
@endif
<div class="profile {{ isset($small) && $small == true ? 'small' : '' }}">
	<img src="{{ $ProfileEmployee -> picture() }}"/>
	<span>{{ $ProfileEmployee -> full_name() }}</span>
</div>
@if (isset($link) && $link == true)
	</a>
@endif