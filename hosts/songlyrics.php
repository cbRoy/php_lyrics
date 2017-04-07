<?php

class Hosts_SongLyrics extends HostAbstract implements HostInterface {

  public function __construct(){
    $this->setUrl('http://www.songlyrics.com');
    $this->setSearchString('/index.php?section=search&searchW=');
    $this->setParseSpec(
      (new ParseSpec('h3 a','.serpdesc-2 a:nth-child(1)', 'h3 a','#songLyricsDiv'))->getSpec()
    );
  }

}


?>
