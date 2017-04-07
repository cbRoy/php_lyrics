<?php

//$posts must be an array of post name/value pairs
function url_get_contents($url,$ref="http://google.com",$posts=false){
	$crl = curl_init();
	$timeout = 5;
	$userAgent  = "Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/28.0.1500.71 Chrome/28.0.1500.71 Safari/537.36";
	curl_setopt ($crl, CURLOPT_USERAGENT,  $userAgent);
	curl_setopt ($crl, CURLOPT_URL, $url);
	curl_setopt ($crl, CURLOPT_ENCODING, 'UTF-8');
	curl_setopt ($crl, CURLOPT_HEADER, false);
	curl_setopt ($crl, CURLOPT_REFERER, $ref);
	curl_setopt ($crl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt ($crl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt ($crl, CURLOPT_AUTOREFERER, true);
	curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
	if($posts){
		curl_setopt ($crl, CURLOPT_POST, true);
		curl_setopt ($crl, CURLOPT_POSTFIELDS, http_build_query($posts));
	}
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
