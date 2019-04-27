@extends('employees.view')

@section('title', $Employee -> full_name() . ' · Files')

@section('employee-content')
	<h3>{{ $Employee -> first_name}}'s Files</h3>
	@if (count($Files) === 0)
		<div style="text-align:center">This employee does not have any files stored.</div>
	@else
		@component('partials.file_table')
			@slot('Files', $Files)
		@endcomponent
	@endif
	
	<h3>Files shared with {{ $Employee -> first_name }}</h3>
	@if (count($SharedFiles) === 0)
		<div style="text-align:center">This employee does not have any files shared with them.</div>
	@else
		@component('partials.file_table')
			@slot('Files', $SharedFiles)
		@endcomponent
	@endif
	
	@if ($UserEmployee == $Employee)
		<div class="spacer px20"></div>
		
		<form enctype="multipart/form-data" method="POST">
			@csrf
			
			<input type="hidden" name="MAX_FILE_SIZE" value="5242880" /> 
			
			<h3 style="margin:0">Upload File</h3>
			
			<div class="spacer px10"></div>
			
			<input type="file" id="file-upload" name="employee-file-upload"/>
			
			<div class="spacer px10"></div>
			
			<span style="font-size:12px" id="max-file-size-warning">(Max 5 MB)</span>
			
			<div class="spacer px10"></div>
			
			@component('partials.button')
				@slot('submit', true)
				@slot('color', 'green')
				@slot('disabled', true)
				@slot('text', 'Upload')
				@slot('icon', 'fas fa-upload')
				@slot('id', 'file-upload-btn')
			@endcomponent
		</form>
	@endif
@endsection

@section('modals')
	<div class="modal share-file-modal">
		<h2 style="margin:0">Share File</h2>
		
		Who would you like to share this file with?
		
		<div class="spacer px10"></div>
		
		<input type="hidden" class="file-id"/>
		<input type="text" class="employee-selector" name="employee-id"/>
		
		<div class="spacer px10"></div>
		
		@component('partials.button')
			@slot('color', 'purple')
			@slot('text', 'Share')
			@slot('icon', 'fas fa-share')
		@endcomponent
	</div>
@endsection