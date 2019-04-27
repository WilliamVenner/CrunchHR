<?php

namespace App;

class Pagination
{
	public static function Page($pages = 1)
	{
		$page = 1;
		if (isset($_POST['pagination-page']) && is_numeric($_POST['pagination-page'])) {
			$page = intval($_POST['pagination-page']);
		} elseif (isset($_POST['pagination-cur-page']) && is_numeric($_POST['pagination-cur-page'])) {
			$page = intval($_POST['pagination-cur-page']);
		}

		if (isset($_POST['pagination-next']) && $page < $pages) {
			$page += 1;
		}
		if (isset($_POST['pagination-prev']) && $page > 1) {
			$page -= 1;
		}

		return $page;
	}
}
