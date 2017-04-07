<?php

class LyricParser {
	public static function parseText($x){	return $x->text();}
	public static function parseLink($x){
		if($x->is('a'))
			return $x->attr('href');
		else
			return $x->parent('a')->attr('href');
	}
}
