<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<TITLE>mobile Lyrics</TITLE>
<STYLE>
.lyrics{
}
.hidden{
	display:none;
}
.show{
	display:block;
}
</STYLE>
<script>
var hidden = false;
function getElementsByClass(searchClass, domNode, tagName) {
	if (domNode == null) domNode = document;
	if (tagName == null) tagName = '*';
	var el = new Array();
	var tags = domNode.getElementsByTagName(tagName);
	var tcl = " "+searchClass+" ";
	for(i=0,j=0; i<tags.length; i++) {
		var test = " " + tags[i].className + " ";
		if (test.indexOf(tcl) != -1)
		el[j++] = tags[i];
	}
	return el;
}

function toggle(clicked,host){
	var div = getElementsByClass("lyrics",null,"div");
	for(var i=0; i<div.length; i++){
		div[i].style.display = 'none';
	}
	var show_div = document.getElementById(host);
	show_div.style.display = 'block';
	var hosts = getElementsByClass("host",null,"a");
	for(var i=0; i<hosts.length; i++){
		hosts[i].style.fontWeight='normal';
	}
	clicked.style.fontWeight='bold';
}
</script>
</HEAD>
<BODY>
<?php
require('../includes/simple_html_dom.php');
include '../includes/phpQuery.php';

$submits = array( "Search", "Lucky!" );
$hosts = array( 
		"LetsSingIt.com" => "http://search.letssingit.com/?a=search&s=",
		"genius.com" => "http://genius.com/search?q=",
		"songlyrics.com" => "http://www.songlyrics.com/index.php?section=search&searchW="
);
if(isset($_POST['host'])){
	$selectedHost = $_POST['host'];
}
$self = $_SERVER['PHP_SELF'];
if(isset($_GET['h'])){ 			//DISPLAY LYRICS
	if(array_key_exists($_GET['h'],$hosts)){
		echo get_lyrics($_GET['h'], get_url_contents($_GET['u']));
	}else{
		echo "oops...";
	}
}else if(!isset($_POST['search'])){		//DISPLAY SEARCH FORM
	echo "<form action=\"$self\" method=post>";
	echo "<input type=text name=search><br>";
	echo "<select name=host>";
	foreach($hosts as $host => $link){
		echo "<option value=\"$host\">$host</option>";
	}
	echo "</select><BR>";
	foreach($submits as $submit){
		echo "<input type=submit name=submit value=" . $submit . ">";
	}
	echo "</form>";
}else{	
		$url = $hosts[$selectedHost];
		$url .= rawurlencode($_POST['search']);
		$raw = get_url_contents($url);
	if($_POST['submit'] != "Lucky!"){
		// show results of all artists/songs
		echo get_results($_POST['host'],$raw);
	}else{
		// show show first result
		echo get_lucky($selectedHost,$raw);
	}
}
function get_lyrics($host,$html){
	global $hosts;
	$lookup = array_keys($hosts);
	switch($host){
		case $lookup[0]:
			//clicked lyrics link
			$dom = new DomDocument;
			@$dom->loadHTML($html);
			$title = $dom->getElementsByTagName('title')->item(0)->textContent;
			$lyrics = $dom->getElementById('lyrics')->nodeValue;
			return $title . "<BR><BR>" . nl2br($lyrics);
		break;
		case $lookup[1]:
			//genius lyrics
			$html = str_get_html($html);
			$title = array_shift($html->find('title'))->innertext;
			$lyrics = array_shift($html->find('.lyrics'))->plaintext;
			return $title . "<BR><BR>" . nl2br($lyrics);
		break;
		case $lookup[2]:
			$dom = new DOMDocument;
			@$dom->loadHTML($html);
			$lyrics = $dom->getElementById('songLyricsDiv')->nodeValue;
			return nl2br($lyrics);
		break;
		default:
			echo "you broke it, not fix it.";
	}
}
function get_lucky($host,$html){
	global $hosts;
	$lookup = array_keys($hosts);
	switch($host){
		case $lookup[0]: //letssingit.com
			$find = "'<table class=data_list>(.*)<\/table>'is";
			preg_match($find,$html,$results);
			$page = str_get_html($results[1]);
			$row = $page->find('table[@=data_list]tr',1); //second row of table!
			$title  = $row->find('td',1);
			preg_match("'<a href=\"(.*)\" class=tt_song.*>(.*)<\/a>'is",$title,$t_link);
			$lyrics_link = $t_link[1];
			return get_lyrics($lookup[0], get_url_contents($lyrics_link)); 
		
break;
		case $lookup[1]: //rapgenius.com
			$page = str_get_html($html);
	
			$row = $page->find('a[class="song_link"]',0); //first result!
			$lyrics_link = $row->href;
			$page->clear(); 
			unset($page);
			$html = file_get_html($lyrics_link);
			return get_lyrics($lookup[1],$html);
break;
	case $lookup[2]:
		$page = str_get_html($html);
		$lyrics_link = $page->find('h3 a',0)->href; //first title
		return get_lyrics($lookup[2], get_url_contents($lyrics_link));
	break;
		default:
			return "Oops! something bad happened while getting lyrics!";
	}
}

function get_results($host,$ret){
	global $hosts;
	$lookup = array_keys($hosts);
	switch($host){	
		case $lookup[0]:  //letssingit.com

	//find main table data list, includes a table with artist and song
	//use Simple HTML Dom Parser
	// find every row in table and grab column artist(0)
	// and title(1) and convert links to redirect back
	$html = str_get_html($ret);
	$tmp = '';
	foreach($html->find('table[class="table_as_list_v2"]tr') as $row){
		$song  = $row->find('td',1);

		$title = $song->find('a',0)->innertext;
		$artist = $song->find('a',1)->innertext;
		$link = $song->find('a',0)->href;
		$tmp .= $artist;
		$tmp .= " - <a href=\"?h=". $lookup[0]."&u=".$link."\">$title lyrics</a> <BR>";
	}
	return $tmp;
break;
/////////////////////////
// END LETSSINGIT.com ///
/////////////////////////

		case $lookup[1]:
/////////////////////////////
//   FOR RAPGENIUS.COM   ////
/////////////////////////////
	
	$html = str_get_html($ret);
	
	foreach($html->find('a[class="song_link"]') as $row) {

		$tmp.= "<a href=\"?h=".$lookup[1]."&u=".$row->href."\">".$row->plaintext."</a><BR>";

	}
	return $tmp;
break;
////////////////////////
//  END RAPGENIUS.COM //
////////////////////////
		case $lookup[2]:
/////////////////////////////
//   FOR LYRICS.COM   ////
/////////////////////////////

	$parseSpec = array(
	"title" => array("h3 a" , array("LyricsSearchParser","parseTitle")),
	"link" => array("h3 a" , array("LyricsSearchParser","parseTitle_link")),
	"artist" => array(".serpdesc-2 a:nth-child(1)" , array("LyricsSearchParser","parseArtist")));
	$page = phpQuery::newDocument($ret);
	$results = parse_document($page, $parseSpec);
	$tmp = '';
	foreach($results as $song){
		$tmp .= $song['artist'] . " - ";
		$tmp .= "<a href=\"?h=". $lookup[2];
		$tmp .= "&u=".$song['link']."\">".$song['title']."</a><br>";
	}
	return $tmp;
break;
////////////////////////
//  END LYRICS.COM //
////////////////////////

		default:
			return "Oops! Something bad happened!<BR>";
	}
}
function parse_document($doc, $selectors) {
	$output = array();
	foreach ($selectors as $name => $selector) {
		foreach ($doc->find($selector[0]) as $k => $v) {
			if (!isset($output[$k])) {
				$output[$k] = array();
			}
			$output[$k][$name] = call_user_func($selector[1], pq($v));
		}
	}
	return $output;
}
class LyricsSearchParser {
	public static function parseTitle($x){		return str_ireplace("lyrics",'',$x->text());}
	public static function parseTitle_link($x){	return $x->attr('href');}
	public static function parseArtist($x){ 	return $x->text();}
}

function get_url_contents($url){
	$crl = curl_init();
	$timeout = 5;
	curl_setopt ($crl, CURLOPT_URL,$url);
	curl_setopt ($crl, CURLOPT_AUTOREFERER, false);
	curl_setopt ($crl, CURLOPT_REFERER, "http://google.com");
	curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
	$ret = curl_exec($crl);
	$info = curl_getinfo($crl);
 
if ($ret === false || $info['http_code'] != 200) {
   $ret = "No cURL data returned for $url [". $info['http_code']. "]";
   if (curl_error($crl))
     $ret .= "\n". curl_error($crl);
}
	curl_close($crl);
	return $ret;
}

?>
</BODY>
</HTML>
