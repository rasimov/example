<?php

defined( '_BASE_' ) or die;

class request {
  function __get($name){
        if (array_key_exists($name, $_REQUEST)) {
            return $_REQUEST[$name];
        }else{return null;}

  }

  function __set($name,$value){
        $_REQUEST[$name]=$value;
  }

  function all(){
        return $_REQUEST;
  }
}    