<?php

interface HostInterface{
  function setUrl($url);
  function getURL();
  function setSearchString($search);
  function getSearchString();
  function setParseSpec($spec);
  function getParseSpec();
  function search($query);
  function getResults();
  function getLucky();
  function parseData();
}
?>
