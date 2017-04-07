<?php

class Hosts_LetsSingIt extends HostAbstract implements HostInterface {

  public function __construct(){
    $this->setUrl('http://search.letssingit.com');
    $this->setSearchString('/?a=search&artist_id=&l=archive&s=');
    $this->setParseSpec(
      (new ParseSpec('.high_profile','a:nth-child(5)', '.high_profile','#lyrics'))->getSpec()
    );
  }

}


?>
