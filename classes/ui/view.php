<?php

defined( '_BASE_' ) or die;

class view {
        var $name        = null;
        var $name_ed     = null;

        function __construct() { }

        function setName($name){
           $this->name = $name[0];
           $this->name_ed = $name[1];
        }

}  
