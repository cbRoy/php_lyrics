<?php
function __autoload($classname){
  if(file_exists($classname.'.php')){
    include $classname . ".php";
  }else{
    $fn = str_replace('_',DIRECTORY_SEPARATOR, strtolower($classname).'.php');
    if(file_exists($fn)){
      include $fn;
    }else{
      echo $fn . " doesn't exist...";
      return false;
    }
  }
}
