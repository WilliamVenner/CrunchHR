@extends('employees.view')

@section('title', $Employee -> full_name() . ' · Keycard')

@section('employee-content-center')
	@if ($Keycard)
		<form method="POST" id="keycard-revoke-form">
			@csrf
			
			<input type="hidden" name="revoke" value=""/>
			
			<table id="keycard-table">
				<thead>
					<tr>
						<th colspan="2">
							<i class="fas fa-id-card"></i>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>Created</td>
						<td>
							@component('partials.timestamp') @slot('timestamp', $Keycard -> created_at) @endcomponent
						</td>
					</tr>
					<tr>
						<td>NFC Tag URL</td>
						<td style="max-width: 400px; overflow: hidden; user-select: all">
							{{ env('APP_URL') }}/api/auth/keycard/authenticate/{{ $Keycard -> secret_key }}
						</td>
					</tr>
					<tr>
						<td>Last IP Address</td>
						<td>
							@if ($Keycard -> last_used_ip_address === null)
								<i>N/A</i>
							@else
								{{ $Keycard -> last_used_ip_address }}
							@endif
						</td>
					</tr>
					<tr>
						<td>Last Used</td>
						<td>
							@if ($Keycard -> last_used === null)
								<i>Never</i>
							@else
								@component('partials.timestamp') @slot('timestamp', $Keycard -> last_used) @endcomponent
							@endif
						</td>
					</tr>
					<tr>
						<td colspan="2">
							@component('partials.button')
								@slot('color', 'red')
								@slot('text', 'Revoke')
								@slot('icon', 'fas fa-ban')
								@slot('id', 'keycard-revoke')
							@endcomponent
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	@else
		This employee does not have a keycard.
		
		<div class="spacer px15"></div>
		
		<form method="POST">
			@csrf
			
			@component('partials.button')
				@slot('submit', true)
				@slot('name', 'create')
				@slot('color', 'green')
				@slot('text', 'Create Keycard')
				@slot('icon', 'fas fa-id-card')
			@endcomponent
		</form>
	@endif
@endsection