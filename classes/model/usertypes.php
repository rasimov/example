<?php

defined( '_BASE_' ) or die;

class usertypes extends DBTable {
        var $id                     = null;
        var $usertype               = null;
        var $typename               = null;
        var $closed                 = null;
        var $st_tema                = null;
        var $st_news                = null;
        var $st_exc                 = null;
        var $st_doc                 = null;

        function __construct( &$database ) {
                $this->DBTable( '#__usertypes', 'id', $database );
        }

        function getTypeName($usertype){
           $this->set('usertype',$usertype);
           $this->_tbl_key = 'usertype';
           $this->load();
           return $this->typename;
        }
}  
