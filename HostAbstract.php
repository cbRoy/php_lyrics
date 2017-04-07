<?php

abstract class HostAbstract implements HostInterface{
  private $m_URL;
  private $m_SearchString;
  private $m_SearchQuery;
  private $m_ParseSpec;
  private $m_RawScrapeData;

  function setUrl($url){
    $this->m_URL = $url;
  }
  function getURL(){
    if(!is_null($this->m_SearchQuery)){
      return $this->m_URL . $this->m_SearchString . $this->m_SearchQuery;
    }
    return $this->m_URL;
  }
  function setSearchString($search){
    $this->m_SearchString = $search;
  }
  function getSearchString(){
    return $this->m_SearchString;
  }

  function setParseSpec($spec){
    $this->m_ParseSpec = $spec;
  }
  function getParseSpec(){
    return $this->m_PraseSpec;
  }

  function search($query){
    $this->m_SearchQuery = $query ?? '';
    $url = $this->getURL();
    $this->m_RawScrapeData = url_get_contents($url);
  }

  function getResults(){
    return $this->parseData('results');
  }
  function getLyrics($url){
    $this->m_RawScrapeData = url_get_contents($url);
    return $this->parseData('lyrics')[0]['lyrics'];
  }
  function getLucky(){
    //return data from first hit on search

  }

  function parseData(){
    $page = phpQuery::newDocument($this->m_RawScrapeData);
    return parse_document($page, $this->m_ParseSpec);
  }
}


?>
