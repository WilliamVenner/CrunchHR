<table class="styled">
	<thead>
		<tr>
			<th>#</th>
			<th>Name</th>
			<th>Type</th>
			<th>Size</th>
			<th>Uploaded</th>
			<th>Uploader</th>
			<th></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		@foreach($Files as $File)
			@component('partials.file_table_row')
				@slot('File', $File)
			@endcomponent
		@endforeach
	</tbody>
</table>