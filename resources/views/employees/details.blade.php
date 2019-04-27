@extends('employees.view')

@section('title', $UserEmployee -> full_name() . ' · Details')

@section('employee-content-center')
	<table id="employee-details">
		<tr>
			<td><label for="job">Job:</label></td>
			<td>
				<select id="job" name="job_id" disabled>
					@if ($UserEmployee -> job)
						<option selected>{{ $UserEmployee -> job -> title }}</option>
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
			<td><input type="text" name="first_name" id="first_name" value="{{ $UserEmployee -> first_name }}" disabled/></td>
		</tr>
		<tr>
			<td><label for="middle_name">Middle Name:</label></td>
			<td><input type="text" name="middle_name" id="middle_name" value="{{ $UserEmployee -> middle_name }}" disabled/></td>
		</tr>
		<tr>
			<td><label for="last_name">Last Name:</label></td>
			<td><input type="text" name="last_name" id="last_name" value="{{ $UserEmployee -> last_name }}" disabled/></td>
		</tr>
		<tr>
			<td><label for="email">Email:</label></td>
			<td><input type="email" name="email" id="email" value="{{ $UserEmployee -> email }}" disabled/></td>
		</tr>
		<tr>
			<td><label for="mobile">Mobile:</label></td>
			<td><input type="tel" name="mobile" id="mobile" placeholder="+44 0000 000000" value="{{ $UserEmployee -> mobile }}" disabled/></td>
			<td><input class="ext" type="text" name="mobile_ext" placeholder="Ext" value="{{ $UserEmployee -> mobile_ext }}" disabled/></td>
		</tr>
		<tr>
			<td><label for="landline">Landline:</label></td>
			<td><input type="tel" name="landline" id="landline" placeholder="01403 000 000" value="{{ $UserEmployee -> landline }}" disabled/></td>
			<td><input class="ext" type="text" name="landline_ext" placeholder="Ext" value="{{ $UserEmployee -> landline_ext }}" disabled/></td>
		</tr>
		<tr>
			<td></td>
			<td><div class="spacer px10"></div></td>
		</tr>
		<tr>
			<td><label for="address_1">Address:</label></td>
			<td>
				<div class="address-form">
					<input type="text" name="address_1" placeholder="Address Line 1" id="address_1" value="{{ $UserEmployee -> address_1 }}" disabled/>
					<input type="text" name="address_2" placeholder="Address Line 2" value="{{ $UserEmployee -> address_2 }}" disabled/>
					<input type="text" name="city" placeholder="City" value="{{ $UserEmployee -> city }}" disabled/>
					<input type="text" name="county" placeholder="County" value="{{ $UserEmployee -> county }}" disabled/>
					<input type="text" name="postcode" placeholder="Postcode" value="{{ $UserEmployee -> postcode }}" disabled/>
					<input type="text" name="country" placeholder="Country" value="{{ $UserEmployee -> country }}" disabled/>
				</div>
			</td>
		</tr>
	</table>
@endsection