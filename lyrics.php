<?php 
//header('Content-type: text/html; charset=utf-8');
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
		"LetsSingIt.com" => "http://search.letssingit.com/cgi-exe/am.cgi?a=search&l=archive&s=",
		"genius.com" => "http://genius.com/search?q=",
		"songlyrics.com" => "http://www.songlyrics.com/index.php?section=search&searchW="
);
if(isset($_POST['host'])){
	$selectedHost = $_POST['host'];
}
$self = $_SERVER['PHP_SELF'];
if(isset($_GET['h'])){ 			//DISPLAY LYRICS
	if(array_key_exists($_GET['h'],$hosts)){
		$host = $_GET['h'];
	}else{
		$host = null;
	}
	$lookup = array_keys($hosts);
	switch($host){
		case $lookup[0]:
			//clicked lyrics link
			$url = $_GET['u'];
			$return = get_url_contents($url);

			$dom = new DomDocument;
			@$dom->loadHTML($return);
			$title = $dom->getElementsByTagName('title')->item(0)->textContent;
			$lyrics = $dom->getElementById('lyrics')->nodeValue;
			echo $title."<BR><BR>";
			echo nl2br($lyrics);
		break;
		case $lookup[1]:
			//rapgenius lyrics
			$url = $_GET['u'];

			$html = file_get_html($url);
			$title = array_shift($html->find('title'))->innertext;
			$lyrics = array_shift($html->find('.lyrics'))->plaintext;
			echo $title."<BR><BR>";
			echo nl2br($lyrics);
		break;
		case $lookup[2]:
			$url = $_GET['u'];
			$html = get_url_contents($url);
			$dom = new DOMDocument;
			@$dom->loadHTML($html);
			$lyrics = $dom->getElementById('songLyricsDiv')->nodeValue;
			echo nl2br($lyrics);
		break;
		default:
			echo "you broke it, not fix it.";
	}
}else if(!isset($_POST['search'])){		//DISPLAY SEARCH FORM
	echo "<form action=\"$self\" method=post>";
	echo "<input type=text name=search><br>";
	echo "<select name=host>";
	foreach($hosts as $host => $link){
		echo "<option value=$host>$host</option>";
	}
	echo "</select><BR>";
	foreach($submits as $submit){
		echo "<input type=submit name=submit value=" . $submit . ">";
	}
	echo "</form>";
}else{	
	if($_POST['submit'] != "Lucky!"){
		//First search for song:
		$url = $hosts[$selectedHost];
		$url .= rawurlencode($_POST['search']);
		$ret = get_url_contents($url);

		echo get_results($_POST['host'],$ret);

	}else{
		//get_results from each host
		//create seperate divs for each host
		//buttons across the top for each host
		//buttons decide which div is shown
		// don't like this model anymore... to intensive upfront.
		// we want faster load times, not slower loads and quicker switches...

		foreach($hosts as $host => $link){ // get results for each host? no way too slow... 
			$url = $link;
			$url .= rawurlencode($_POST['search']);
			$ret = get_url_contents($url);

			$results[$host] = get_lucky($host,$ret);
			echo " <a href class=\"host\" onClick=\"event.preventDefault();toggle(this,'$host');\">[ " . $host . " ]</a> ";
		}
		echo "<BR>";
		$i=0;
		foreach($results as $host => $lyrics){
			echo "<DIV class=\"lyrics\"" . (($i++ == 0) ? "" : " style=\"display:none;\""); 
			echo " id=\"$host\">";
			echo $lyrics;
			echo "</div>";
		}

		//echo '<pre>l';
		//echo print_r($results);
		//echo 'k</pre>';

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
			$page->clear(); 
			unset($page);
			$return = get_url_contents($lyrics_link);
			$dom = new DomDocument;
			@$dom->loadHTML($return);
			$title = $dom->getElementsByTagName('title')->item(0)->textContent;
			$lyrics = $dom->getElementById('lyrics')->nodeValue;
			$tmp = $title."<BR><BR>";
			return $tmp .= nl2br($lyrics);
		
break;
		case $lookup[1]: //rapgenius.com
			$page = str_get_html($html);
	
			$row = $page->find('a[class="song_link"]',0); //first result!
			$lyrics_link = $row->href;
			$page->clear(); 
			unset($page);
			$html = file_get_html($lyrics_link);
			$title = array_shift($html->find('title'))->innertext;
			$lyrics = array_shift($html->find('.lyrics'))->plaintext;
			$tmp = $title."<BR><BR>";
			$html->clear(); 
			unset($html);
			return $tmp.= nl2br($lyrics);
break;
	case $lookup[2]:
		$page = str_get_html($html);
		$lyrics_link = $page->find('h3 a',0)->href; //first title
		$page->clear();
		unset($page);
		$html = get_url_contents($lyrics_link);
		$dom = new DOMDocument;
		@$dom->loadHTML($html);
		$title = $dom->getElementsByTagName('title')->item(0)->nodeValue;
		$lyrics = $dom->getElementById('songLyricsDiv')->nodeValue;
		return $title . "<BR><BR>" . nl2br($lyrics);
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
	$find = "'<table class=data_list>(.*)<\/table>'is";
	preg_match($find,$ret,$results);

	//use Simple HTML Dom Parser
	// find every row in table and grab column artist(0)
	// and title(1) and convert links to redirect back
	$html = str_get_html($results[1]);
	$tmp = '';
	foreach($html->find('table[@class=data_list]tr') as $row){
		$artist = $row->find('td',0);
		$title  = $row->find('td',1);
		if($title != null && $title != "<td>&nbsp;</td>" && !strstr($title,"tt_album_")){
			preg_match("'<a href=\"(.*)\" class=tt_artist.*>(.*)<\/a>'is",$artist,$a_link);
			preg_match("'<a href=\"(.*)\" class=tt_song.*>(.*)<\/a>'is",$title,$t_link);

			#$tmp.= "<a href=\"".$self."?a=".$a_link[1]."\">$a_link[2]</a> "; //not really used right now
			$tmp .= $a_link[2]; //artist name
			$tmp.= " - <a href=\"?h=". $lookup[0]."&u=".$t_link[1]."\">$t_link[2] lyrics</a> <BR>";
		}
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
