<? //generate an RSS feed for index pages

include "shared.php";

/* ====================================================================================================================== */

//which folder to show, not present for forum index
if ($path = preg_match ('/([^.\/]+)\//', @$_GET['path'], $_) ? $_[1] : '') chdir (APP_ROOT.$path);

//get list of threads
$threads = array_fill_keys (preg_grep ('/\.xml$/', scandir ('.')) , 0);
foreach ($threads as $file => &$date) $date = filectime ($file);
arsort ($threads, SORT_NUMERIC);

$threads = array_slice ($threads, 0, APP_THREADS);
foreach ($threads as $file => $date) {
	$xml = simplexml_load_file ($file);
	$items = $xml->channel->xpath ('item');
	$item = end ($items);
	
	@$rss .= template_tags (TEMPLATE_RSS_ITEM, array (
		'TITLE'	=> safetext ($xml->channel->title),
		'URL'	=> ($path ? rawurlencode ($path).'/' : '').pathinfo ($file, PATHINFO_FILENAME),
		'NAME'	=> safetext ($item->author),
		'DATE'	=> gmdate ('r', strtotime ($item->pubDate)),
		'TEXT'	=> safetext ($item->description),
	));
}

header ("Content-Type: application/rss+xml;charset=UTF-8");
die (template_tags (TEMPLATE_RSS_INDEX, array (
	'PATH'	=> $path ? rawurlencode ($path).'/' : '',
	'TITLE'	=> $path ? safetext ($path) : "Forum Index",
	'ITEMS'	=> $rss
)));

?>