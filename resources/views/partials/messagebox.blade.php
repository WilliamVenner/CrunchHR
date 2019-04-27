@if (isset($writing) && $writing == true)
	<div class="messagebox">
		<div class="profile-container">
			@component('partials.employee_profile')
				@slot('employee', $UserEmployee)
			@endcomponent
		</div>
		<div class="content">
			<textarea required placeholder="{{ $placeholder }}" name="content" oninput='this.style.height = "";this.style.height = this.scrollHeight + 3 + "px"'></textarea>
			<button type="submit" class="submit" name="new">
				<div>
					<i class="fas fa-share-square"></i>
				</div>
			</button>
		</div>
	</div>
@else
	<div class="messagebox" {!! isset($id) ? 'data-id="' . $id . '"' : '' !!}>
		<div class="profile-container">
			@component('partials.employee_profile')
				@slot('employee_id', $employee_id)
				@slot('link', true)
			@endcomponent
			
			@if (isset($delete) && $delete == true)
				<button type="submit" class="delete" name="delete" value="{{ $id }}">
					<i class="fas fa-times-circle"></i>
				</button>
			@endif
		</div>
		<div class="content" style="padding:10px;display:block">
			{{ $content }}
			<br>
			<div class="created-at">@component('partials.timestamp') @slot('timestamp', $created_at) @endcomponent</div>
		</div>
	</div>
@endif