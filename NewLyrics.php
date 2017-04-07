<?php
error_reporting(-1);
ini_set("display_errors", 1);


include 'autoload.php';
include '../includes/phpQuery.php';
include 'functions.php';

$submits = array( "Search", "Lucky!" );
$hosts = [
  'genius' => new Hosts_Genius(),
  'letssingit' => new Hosts_LetsSingIt(),
  'songlyrics' => new Hosts_SongLyrics(),
];

$selectedHost = $_POST['host'] ?? 'genius';

$self = $_SERVER['PHP_SELF'];
if(isset($_GET['h'])){ 			//DISPLAY LYRICS
	if(array_key_exists($_GET['h'],$hosts)){
		echo "<pre>".$hosts[$_GET['h']]->getLyrics($_GET['u'])."</pre>";
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
		$hosts[$selectedHost]->search(rawurlencode($_POST['search']));
	if($_POST['submit'] != "Lucky!"){
		// show results of all artists/songs
		$results= $hosts[$selectedHost]->getResults();
    foreach($results as $result){
      if(isset($result['artist']) && isset($result['title'])){
        echo "<a href=\"?h=".$selectedHost."&u=".$result['link']."\">";
        echo $result['artist'] . " - " . $result['title'] . "</a><br>";
      }
    }
	}else{
		// show show first result
		echo $hosts[$selectedHost]->getLucky();
	}
}

function get_lyrics($host, $url){
  $results= $hosts[$host]->getLyrics($url);
}




?>
