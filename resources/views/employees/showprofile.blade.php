@extends('employees.view')

@section('title', $Employee -> full_name() . ' · Details')

@section('employee-content-center')
	<table id="employee-details">
		<tr>
			<td><label for="job">Job:</label></td>
			<td>
				<select id="job" name="job_id" disabled>
					@if ($Employee -> job)
						<option selected>{{ $Employee -> job -> title }}</option>
					@else
						<option selected>No job</option>
					@endif
				</select>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><div class="spacer px10"></div></td>
		</tr>
		<tr>
			<td><label for="first_name">First Name:</label></td>
			<td><input type="text" name="first_name" id="first_name" value="{{ $Employee -> first_name }}" disabled/></td>
		</tr>
		<tr>
			<td><label for="middle_name">Middle Name:</label></td>
			<td><input type="text" name="middle_name" id="middle_name" value="{{ $Employee -> middle_name }}" disabled/></td>
		</tr>
		<tr>
			<td><label for="last_name">Last Name:</label></td>
			<td><input type="text" name="last_name" id="last_name" value="{{ $Employee -> last_name }}" disabled/></td>
		</tr>
		<tr>
			<td><label for="email">Email:</label></td>
			<td><input type="email" name="email" id="email" value="{{ $Employee -> email }}" disabled/></td>
		</tr>
		<tr>
			<td><label for="mobile">Mobile:</label></td>
			<td><input type="tel" name="mobile" id="mobile" placeholder="+44 0000 000000" value="{{ $Employee -> mobile }}" disabled/></td>
			<td><input class="ext" type="text" name="mobile_ext" placeholder="Ext" value="{{ $Employee -> mobile_ext }}" disabled/></td>
		</tr>
		<tr>
			<td><label for="landline">Landline:</label></td>
			<td><input type="tel" name="landline" id="landline" placeholder="01403 000 000" value="{{ $Employee -> landline }}" disabled/></td>
			<td><input class="ext" type="text" name="landline_ext" placeholder="Ext" value="{{ $Employee -> landline_ext }}" disabled/></td>
		</tr>
	</table>
@endsection