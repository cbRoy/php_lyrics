<?php

class ParseSpec{

	private $link;
	private $artist;
	private $title;
	private $lyrics;


	public function __construct($link, $artist, $title, $lyrics){
		$this->link = $link;
		$this->artist = $artist;
		$this->title = $title;
		$this->lyrics = $lyrics;
	}

	public function getSpec(){//convert to lyrics
		$array = array(
			'link' => array($this->link, array('LyricParser','parseLink')),
			'artist' => 	array( $this->artist, 	array('LyricParser', 'parseText')),
			'title' => array( $this->title, array('LyricParser', 'parseText')),
		);
		if($this->lyrics !== null)
			$array['lyrics'] = array( $this->lyrics, 	array('LyricParser', 'parseText'));

		return $array;
	}
}
