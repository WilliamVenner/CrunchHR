@extends('employees.view')

@section('title', $Employee -> full_name() . ' · Details')

@section('employee-content-center')
	<form method="POST" enctype="multipart/form-data">
		{{ csrf_field() }}
		
		<div class="h-center"><button type="submit" class="btn green" id="save-employee-details">Save Details</button></div>
		<div class="spacer px15"></div>
		
		<table id="employee-details">
			@if ($UserEmployee->is_human_resources())
				<tr>
					<td><label for="job">Job:</label></td>
					<td>
						<select id="job" name="job_id">
							<option selected disabled value="NULL">Select a job...</option>
							<option value="NULL">No job</option>
							<?php $Departments = \App\Models\Department::orderBy('name', 'ASC')->get(); ?>
							@if (count($Departments) === 0)
								<option disabled>There are no jobs available</option>
							@else
								@foreach(\App\Models\Department::all() as $Department)
									<optgroup label="{{ $Department -> name }}">
										@foreach($Department -> jobs() -> orderBy('title', 'ASC') -> get() as $Job)
											<option value="{{ $Job -> id }}" {{ isset($Employee -> job) && $Job -> id === $Employee -> job -> id ? 'selected' : '' }}>{{ $Job -> title }}</option>
										@endforeach
									</optgroup>
								@endforeach
							@endif
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td><div class="spacer px10"></div></td>
				</tr>
			@endif
			<tr>
				<td>Profile Picture:</td>
				<td>
					<input type="file" name="avatar" class="avatar" accept="image/png,image/jpeg" style="width:181px"/>
					<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
				</td>
				<td class="avatar-errors">
					<div class="filetype">PNG/JPG only</div>
					<div class="filesize">Max 2 MB</div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><div class="spacer px10"></div></td>
			</tr>
			<tr>
				<td><label for="first_name">First Name:</label></td>
				<td><input type="text" name="first_name" id="first_name" value="{{ $Employee -> first_name }}" required/></td>
			</tr>
			<tr>
				<td><label for="middle_name">Middle Name:</label></td>
				<td><input type="text" name="middle_name" id="middle_name" value="{{ $Employee -> middle_name }}"/></td>
			</tr>
			<tr>
				<td><label for="last_name">Last Name:</label></td>
				<td><input type="text" name="last_name" id="last_name" value="{{ $Employee -> last_name }}" required/></td>
			</tr>
			<tr>
				<td><label for="email">Email:</label></td>
				<td><input type="email" name="email" id="email" value="{{ $Employee -> email }}"/></td>
			</tr>
			<tr>
				<td><label for="mobile">Mobile:</label></td>
				<td><input type="tel" name="mobile" id="mobile" placeholder="+44 0000 000000" value="{{ $Employee -> mobile }}" pattern="^(?:^\+(?!0)\d{1,4}|0)(?<!^\+0)\s*(?:\d *){10}(?<! )$" title="Must be a valid national or international phone number format" maxlength="15"/></td>
				<td><input class="ext" type="text" name="mobile_ext" placeholder="Ext" pattern="^\+?\d+$" maxlength="15" value="{{ $Employee -> mobile_ext }}"/></td>
			</tr>
			<tr>
				<td><label for="landline">Landline:</label></td>
				<td><input type="tel" name="landline" id="landline" placeholder="01403 000 000" value="{{ $Employee -> landline }}" pattern="^(?:^\+(?!0)\d{1,4}|0)(?<!^\+0)\s*(?:\d *){10}(?<! )$" title="Must be a valid national or international phone number format" maxlength="15"/></td>
				<td><input class="ext" type="text" name="landline_ext" placeholder="Ext" pattern="^\+?\d+$" maxlength="15" value="{{ $Employee -> landline_ext }}"/></td>
			</tr>
			<tr>
				<td></td>
				<td><div class="spacer px10"></div></td>
			</tr>
			<tr>
				<td><label for="address_1">Address:</label></td>
				<td>
					<div class="address-form">
						<input type="text" name="address_1" placeholder="Address Line 1" id="address_1" value="{{ $Employee -> address_1 }}"/>
						<input type="text" name="address_2" placeholder="Address Line 2" value="{{ $Employee -> address_2 }}"/>
						<input type="text" name="city" placeholder="City" value="{{ $Employee -> city }}"/>
						<input type="text" name="county" placeholder="County" value="{{ $Employee -> county }}"/>
						<input type="text" name="postcode" placeholder="Postcode" value="{{ $Employee -> postcode }}"/>
						<input type="text" name="country" placeholder="Country" value="{{ $Employee -> country }}"/>
					</div>
				</td>
			</tr>
		</table>
	</form>
@endsection