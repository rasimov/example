<?php

defined( '_BASE_' ) or die;

class users extends DBTable {
        var $id                     = null;
        var $name                   = null;
        var $username               = null;
        var $password               = null;
        var $usertype               = null;
        var $lastvisit              = null;
        var $_typename              = null;

        function __construct( &$database ) {
                $this->DBTable( '#__users', 'id', $database );
        }

        function check($name, $password){
           $this->set('name',mysql_escape_string($name));
           $this->_tbl_key = 'name';
           $this->load();
           if (is_null($this->id) || $this->password!=md5(mysql_escape_string($password)) || $this->usertype < 2 ) {
             return false;
           }else{
             return true;
           }
        }
        function setTypeName(){
                $usertype = new usertypes($this->_db);
                $this->_typename = $usertype->getTypeName($this->usertype);
        }

        function getaccess(){
                return($this->usertype);
        }
       
        function findUser($username){
                $this->load(array("usertype"=>array(">"=>1),"username"=>array('like'=>"%".$username."%")));
        }
        function getList($search='',$add=null){
                $add=array_merge(array("sel"=>array("#__usertypes.typename"),"join"=>array("i"=>"#__usertypes","on"=>array("usertype"=>"#__usertypes.usertype"))),$add);
                return $this->loadList(array("username"=>array("like"=>"'%".$search."%'"),"#__users.usertype"=>array("!="=>1)) ,$add );
        } 
        function getCount($search='',$add=array()){
                $add=array_merge(array("sel"=>array("#__usertypes.typename"),"join"=>array("i"=>"#__usertypes","on"=>array("usertype"=>"#__usertypes.usertype"))),$add);
                return $this->count(array("username"=>array("like"=>"'%".$search."%'"),"#__users.usertype"=>array("!="=>1)) ,$add );
        } 
}  
