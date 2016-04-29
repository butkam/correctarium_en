<?php

class Model
{
	public $menuItems = [
		"/how-it-works/" 	=> "Как работает",
		"/price/"					=> "Цены",
		"/who/"						=> "Наши редакторы",
		"/about/"					=> "О проекте"
	];

	public function getCurrentMenu()
	{
		$uri = $_SERVER['REQUEST_URI'];
		$items = [];
		foreach ($this->menuItems as $menuItem => $text) {
			if ($menuItem === $uri) {
				$item = '<li class="menu-item active">' . $text . "</li>\n";
			} else {
				$item = '<li class="menu-item"><a href="' . $menuItem . '">' . $text . "</a></li>\n";
			}
			array_push($items, $item);
		}
		return $items;
	}

	public function getSeoTags($pageName)
	{
		$uri = $_SERVER['REQUEST_URI'];
		$content = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/library/seo_tags.json');
		$json = json_decode($content);

		return $tags = <<<EOT
		<title>{$json->$pageName->title} | Correctarium</title>
		<meta name="description" content="{$json->$pageName->description}">
		<meta name="keywords" content="{$json->$pageName->keywords}">
EOT;
	}
}
