<?php

class View
{
	function generate(
		$content_view,
		$template_view,
		$tags,
		$items = null,
		$data = null,
		$session = null
	) {
		include ('application/views/'.$template_view);
	}
}
