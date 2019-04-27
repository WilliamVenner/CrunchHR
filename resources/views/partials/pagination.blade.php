<?php
	$pages = intval($pages);

	$page = \App\Pagination::Page($pages);
?>
<input type="hidden" name="pagination-cur-page" value="{{ $page }}">
<div class="pagination">
	<button class="page-nav" name="pagination-prev" {{ $page === 1 ? 'disabled' : '' }}>< Prev</button>
	<div>
		@if ($page < 7)
			@for($i = 1; $i <= min($pages, 7); $i++)
				<button class="page {{ $page === $i ? 'active' : '' }}" name="pagination-page" value="{{ $i }}">{{ $i }}</button>
			@endfor
			@if ($pages > 7)
				<div class="page-separator">..</div>
				<button class="page" name="pagination-page" value="{{ $pages - 1 }}">{{ $pages - 1 }}</button>
				<button class="page" name="pagination-page" value="{{ $pages }}">{{ $pages }}</button>
			@endif
		@elseif ($page > $pages - 6)
			<button class="page" name="pagination-page" value="1">1</button>
			<button class="page" name="pagination-page" value="2">2</button>
			<div class="page-separator">..</div>
			@for($i = $pages - 6; $i <= $pages; $i++)
				<button class="page {{ $page === $i ? 'active' : '' }}" name="pagination-page" value="{{ $i }}">{{ $i }}</button>
			@endfor
		@else
			<button class="page" name="pagination-page" value="1">1</button>
			<button class="page" name="pagination-page" value="2">2</button>
			<div class="page-separator">..</div>
			@for($i = $page - 3; $i <= $page + 3; $i++)
				<button class="page {{ $page === $i ? 'active' : '' }}" name="pagination-page" value="{{ $i }}">{{ $i }}</button>
			@endfor
			<div class="page-separator">..</div>
			<button class="page" name="pagination-page" value="{{ $pages - 1 }}">{{ $pages - 1 }}</button>
			<button class="page" name="pagination-page" value="{{ $pages }}">{{ $pages }}</button>
		@endif
	</div>
	<button class="page-nav" name="pagination-next" {{ $page === $pages || $pages === 0 ? 'disabled' : '' }}>Next ></button>
</div>