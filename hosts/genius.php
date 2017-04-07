<?php

class Hosts_Genius extends HostAbstract implements HostInterface {

  public function __construct(){
    $this->setUrl('http://genius.com');
    $this->setSearchString('/search?q=');
    $this->setParseSpec(
      (new ParseSpec('.song_link','.primary_artist_name', '.song_title','.song_body-lyrics'))->getSpec()
    );
  }

}


?>
