@extends('layout')

@section('content-box-title', 'New Employee')
@section('content-box-icon', 'fas fa-user-plus')
@section('content-box')
	<form method="POST" enctype="multipart/form-data" id="recruitment">
		{{ csrf_field() }}
		<div class="split">
			<div class="avatar">
				<img src="/assets/img/employee.png"/><br>
				<input type="file" name="avatar" accept="image/png,image/jpeg">
				<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
				<div class="spacer px10"></div>
				<div id="avatar-errors">
					<div class="filetype">PNG/JPG only</div><br>
					<div class="filesize">Max 2 MB</div>
				</div>
				<div id="avatar-remove" class="hidden"><i class="fas fa-trash-alt"></i> Remove</div>
			</div>
			<div class="details">
				<table>
					<tr>
						<td><label for="first_name">First Name:</label></td>
						<td><input type="text" name="first_name" id="first_name" required/></td>
					</tr>
					<tr>
						<td><label for="middle_name">Middle Name:</label></td>
						<td><input type="text" name="middle_name" id="middle_name"/></td>
					</tr>
					<tr>
						<td><label for="last_name">Last Name:</label></td>
						<td><input type="text" name="last_name" id="last_name" required/></td>
					</tr>
					<tr>
						<td><label for="email">Email:</label></td>
						<td><input type="email" name="email" id="email"/></td>
					</tr>
					<tr>
						<td><label for="mobile">Mobile:</label></td>
						<td><input type="tel" name="mobile" id="mobile" placeholder="+44 0000 000000" pattern="^(?:^\+(?!0)\d{1,4}|0)(?<!^\+0)\s*(?:\d *){10}(?<! )$" title="Must be a valid national or international phone number format" maxlength="15"/><input class="ext" type="text" name="mobile_ext" placeholder="Ext" pattern="^\+?\d+$" maxlength="15"/></td>
					</tr>
					<tr>
						<td><label for="landline">Landline:</label></td>
						<td><input type="tel" name="landline" id="landline" placeholder="01403 000 000" pattern="^(?:^\+(?!0)\d{1,4}|0)(?<!^\+0)\s*(?:\d *){10}(?<! )$" title="Must be a valid national or international phone number format" maxlength="15"/><input class="ext" type="text" name="landline_ext" placeholder="Ext" pattern="^\+?\d+$" maxlength="15"/></td>
					</tr>
					<tr>
						<td></td>
						<td><div class="spacer px10"></div></td>
					</tr>
					<tr>
						<td><label for="address_1">Address:</label></td>
						<td>
							<div class="address-form">
								<input type="text" name="address_1" placeholder="10 Downing Street" id="address_1"/>
								<input type="text" name="address_2" placeholder="Prime Minister's Office"/>
								<input type="text" name="city" placeholder="Westminster"/>
								<input type="text" name="county" placeholder="London"/>
								<input type="text" name="postcode" placeholder="SW1A 2AA"/>
								<input type="text" name="country" placeholder="United Kingdom"/>
							</div>
						</td>
					</tr>
					<tr>
						<td></td>
						<td><div class="spacer px10"></div></td>
					</tr>
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
												<option value="{{ $Job -> id }}">{{ $Job -> title }}</option>
											@endforeach
										</optgroup>
									@endforeach
								@endif
							</select>
						</td>
					</tr>
				</table>
			</div>
		</div>
		
		<div class="spacer px20"></div>
		<div class="h-center"><button type="submit" id="recruit-employee" name="recruit" class="btn green">Recruit Employee</button></div>
	</form>
@endsection

@section('css-includes')
	<link rel="stylesheet" href="/assets/css/recruitment.new.css" type="text/css" />
@endsection

@section('js-includes')
	<script type="text/javascript" src="/assets/js/recruitment.new.js"></script>
@endsection