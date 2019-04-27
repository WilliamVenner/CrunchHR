<tr data-id="{{ $File -> id }}">
	<td>{{ $File -> id }}</td>
	<td style="word-break:break-word">{{ $File -> file_name }}</td>
	<td style='text-align:center'>{{ \App\Helpers::HumanMIMEType($File -> file_mime_type) }}{{ $File -> file_type !== null ? ' (.' . $File -> file_type . ')' : '' }}</td>
	<td style='text-align:center'>{{ \App\Helpers::FormatFileSize($File -> file_size) }}</td>
	<td style='text-align:center'>
		@component('partials.timestamp')
			@slot('timestamp', $File -> created_at)
		@endcomponent
	</td>
	<td style='text-align:center'>
		@component('partials.employee_profile')
			@slot('employee_id', $File -> employee_id)
			@slot('link', true)
		@endcomponent
	</td>
	<td style='text-align:center'>
		@component('partials.button')
			@slot('color', 'green')
			@slot('text', 'Download')
			@slot('icon', 'fas fa-download')
			@slot('link', '/file/' . $File -> id)
		@endcomponent
	</td>
	<td style='text-align:center'>
		@if ($File -> employee_id == $UserEmployee -> id)
			@component('partials.button')
				@slot('color', 'purple')
				@slot('text', 'Share')
				@slot('icon', 'fas fa-share')
				@slot('class', 'share-file')
			@endcomponent
		@endif
	</td>
</tr>