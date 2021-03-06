<?php
//##copyright##

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'header-lite.php');
require_once(ESYN_INCLUDES."util.php");

$from	= isset($_GET['from']) && !empty($_GET['from']) ? $_GET['from'] : NULL;
$limit	= isset($_GET['limit']) && !empty($_GET['limit']) ? (int)$_GET['limit'] : 10;
$out	= '';

if(NULL == $from)
{
	header("HTTP/1.1 404 Not found");
	die("404 Not found. Powered by eSyndicat");
}

$out .= '<?xml version="1.0" encoding="utf-8"?>';
$out .= '<rss version="2.0">';
$out .= '<channel>';

$out .= '<image>';
$out .= '<url>' . ESYN_URL . 'templates/' . $esynConfig->getConfig('tmpl') . '/img/feed-esyndicat.png</url>';
$out .= '<title>eSyndiCat Directory v' . ESYN_VERSION . '</title>';
$out .= '<link>' . ESYN_URL . '</link>';
$out .= '</image>';

if((is_array($from) && in_array('category', $from)) || ('category' == $from))
{
	$eSyndiCat->factory("Category", "Listing", "Layout");

	require_once(ESYN_CLASSES.'esynUtf8.php');

	esynUtf8::loadUTF8Core();
	esynUtf8::loadUTF8Util('utf8_to_ascii');

	$category_id = isset($_GET['id']) && !empty($_GET['id']) ? (int)$_GET['id'] : 0;

	$category = $esynCategory->row("*", "`id` = '{$category_id}'");
	
	$out .= '<title>' . esynSanitize::html($category['title']) . '</title>';
    $out .= '<description>' . esynSanitize::html(strip_tags($category['description'])) . '</description>';
    $out .= '<link>';
	
	$out .= $esynLayout->printCategoryUrl(array('cat' => $category));

    $out .= '</link>';

    // Get link for the selected category
    $listings = $esynListing->getListingsByCategory($category_id, $limit, false, false, 0);

	if(!empty($listings))
	{
		foreach ($listings as $key => $value)
		{
			$item['title'] = $value['title'];

			if(empty($category['path']) && empty($value['path']))
			{
				$path = '';
			}
			else
			{
				$path = (!empty($category['path'])) ? $category['path'].'/' : $value['path'].'/';
			}
			
			if ($esynConfig->getConfig('mod_rewrite'))
			{
				$value['title'] = utf8_to_ascii($value['title']);
				$value['title'] = preg_replace('/[^A-Za-z0-9]+/u', '-', $value['title']);
				$value['title'] = preg_replace('/\-+/', '-', $value['title']);
				$value['title'] = trim($value['title'], '-');
		
				$item['link'] = ESYN_URL . $path . $value['title'] . '-l' . $value['id'] . '.html';
			}
			else
			{
				$item['link'] = ESYN_URL . 'view-listing.php?id=' . $value['id'];
			}

			$item['description'] = $value['description'];
			$item['date'] = $value['date'];

			$out .= create_rss_item($item);
		}
	}
}

if((is_array($from) && in_array('new', $from)) || ('new' == $from))
{
	$eSyndiCat->factory("Listing");

	require_once(ESYN_CLASSES.'esynUtf8.php');

	esynUtf8::loadUTF8Core();
	esynUtf8::loadUTF8Util('utf8_to_ascii');

	$out .= '<title>' . $esynI18N['new_listings'] . '</title>';
    $out .= '<description>' . $esynI18N['newly_added_listings'] . '</description>';
    $out .= '<link>' . ESYN_URL;
    $out .= $esynConfig->getConfig('mod_rewrite') ? 'new-listings.html' : 'new-listings.php';
    $out .= '</link>';

    $listings = $esynListing->getLatest($start = 0, $limit);

	if(!empty($listings))
	{
		foreach ($listings as $key => $value)
		{
			$item['title'] = $value['title'];

			if(empty($category['path']) && empty($value['path']))
			{
				$path = '';
			}
			else
			{
				$path = (!empty($category['path'])) ? $category['path'].'/' : $value['path'].'/';
			}
			
			if ($esynConfig->getConfig('mod_rewrite'))
			{
				$value['title'] = utf8_to_ascii($value['title']);
				$value['title'] = preg_replace('/[^A-Za-z0-9]+/u', '-', $value['title']);
				$value['title'] = preg_replace('/\-+/', '-', $value['title']);
				$value['title'] = trim($value['title'], '-');
		
				$item['link'] = ESYN_URL . $path . $value['title'] . '-l' . $value['id'] . '.html';
			}
			else
			{
				$item['link'] = ESYN_URL . 'view-listing.php?id=' . $value['id'];
			}

			$item['description'] = $value['description'];
			$item['date'] = $value['date'];

			$out .= create_rss_item($item);
		}
	}
}

if((is_array($from) && in_array('popular', $from)) || ('popular' == $from))
{
	$eSyndiCat->factory("Listing");

	require_once(ESYN_CLASSES.'esynUtf8.php');

	esynUtf8::loadUTF8Core();
	esynUtf8::loadUTF8Util('utf8_to_ascii');
	
	$out .= '<title>' . $esynI18N['popular_listings'] . '</title>';
    $out .= '<description>' . $esynI18N['most_popular_listings'] . '</description>';
    $out .= '<link>' . ESYN_URL;
    $out .= $esynConfig->getConfig('mod_rewrite') ? 'popular-listings.html' : 'popular-listings.php';
    $out .= '</link>';

    $listings = $esynListing->getPopular($start = 0, $limit);

	if(!empty($listings))
	{
		foreach ($listings as $key => $value)
		{
			$item['title'] = $value['title'];

			if(empty($category['path']) && empty($value['path']))
			{
				$path = '';
			}
			else
			{
				$path = (!empty($category['path'])) ? $category['path'].'/' : $value['path'].'/';
			}
			
			if ($esynConfig->getConfig('mod_rewrite'))
			{
				$value['title'] = utf8_to_ascii($value['title']);
				$value['title'] = preg_replace('/[^A-Za-z0-9]+/u', '-', $value['title']);
				$value['title'] = preg_replace('/\-+/', '-', $value['title']);
				$value['title'] = trim($value['title'], '-');
		
				$item['link'] = ESYN_URL . $path . $value['title'] . '-l' . $value['id'] . '.html';
			}
			else
			{
				$item['link'] = ESYN_URL . 'view-listing.php?id=' . $value['id'];
			}

			$item['description'] = $value['description'];
			$item['date'] = $value['date'];

			$out .= create_rss_item($item);
		}
	}
}

if((is_array($from) && in_array('top', $from)) || ('top' == $from))
{
	$eSyndiCat->factory("Listing");

	require_once(ESYN_CLASSES.'esynUtf8.php');

	esynUtf8::loadUTF8Core();
	esynUtf8::loadUTF8Util('utf8_to_ascii');
	
	$out .= '<title>' . $esynI18N['top_listings'] . '</title>';
    $out .= '<description>' . $esynI18N['top_listings'] . '</description>';
    $out .= '<link>' . ESYN_URL;
    $out .= $esynConfig->getConfig('mod_rewrite') ? 'top-listings.html' : 'top-listings.php';
    $out .= '</link>';

    $listings = $esynListing->getTop($start = 0, $limit);

	if(!empty($listings))
	{
		foreach ($listings as $key => $value)
		{
			$item['title'] = $value['title'];

			if(empty($category['path']) && empty($value['path']))
			{
				$path = '';
			}
			else
			{
				$path = (!empty($category['path'])) ? $category['path'].'/' : $value['path'].'/';
			}
			
			if ($esynConfig->getConfig('mod_rewrite'))
			{
				$value['title'] = utf8_to_ascii($value['title']);
				$value['title'] = preg_replace('/[^A-Za-z0-9]+/u', '-', $value['title']);
				$value['title'] = preg_replace('/\-+/', '-', $value['title']);
				$value['title'] = trim($value['title'], '-');
		
				$item['link'] = ESYN_URL . $path . $value['title'] . '-l' . $value['id'] . '.html';
			}
			else
			{
				$item['link'] = ESYN_URL . 'view-listing.php?id=' . $value['id'];
			}

			$item['description'] = $value['description'];
			$item['date'] = $value['date'];

			$out .= create_rss_item($item);
		}
	}
}

$eSyndiCat->startHook("feed");

function create_rss_item($item)
{
	$out = '';

	$out .= '<item>';
	
	$out .= '<title>' . esynSanitize::html($item['title']) . '</title>';
	$out .= '<link>' . $item['link'] . '</link>';
	$out .= '<description>' . esynSanitize::html($item['description']) . '</description>';
	$out .= '<pubDate> '. date("D, d M Y H:m:s T", strtotime($item['date'])) . '</pubDate>';
	
	$out .= '</item>';
			
	return $out;
}

$out .= '</channel>';
$out .=  '</rss>';

header('Content-Type: text/xml');

echo $out;
// to prevent auto append files
die();
