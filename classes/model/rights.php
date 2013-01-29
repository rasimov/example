<?php

defined( '_BASE_' ) or die;

class rights extends DBTable {
        var $id                    = null;
        var $table_name            = null;
        var $view                  = null;
        var $edit                  = null;
        var $add                   = null;
        var $delete_               = null;
        var $user_id               = null;
        var $description           = null;
        var $_rights               = null;
        var $_user                 = null;

        function __construct( &$database,&$user = null ) {
                $this->DBTable( '#__rights', 'id', $database );
                $this->_user = $user;
        }

        function getUserRights(){
                if(is_null($this->_user->id)) return;
                $this->_rights = $this->loadList(array("user_id"=>$this->_user->id));
        }

        function changeUser($user){
                $this->_user = $user;
                $this->_rights = $this->loadList(array("user_id"=>$this->_user->id));
        }

        function getUserRight($table,$type){
                if($this->_user->usertype=='4'){return(1);}
                if(is_null($this->_rights)){ $this->getUserRights(); }
                if(is_null($this->_rights)) return false;
                $right = array_filter($this->_rights,function($var) use($table,$type){
                                                           if(($var->table_name == $table)&&($var->$type==1)){
                                                              return true;
                                                           }
                                                              return false;
                                                     });
                return count($right)>0 ? true : false;
        }

        function gvr($table){
                return $this->getUserRight($table,'view');
        }

        function gar($table){
                return $this->getUserRight($table,'add');
        }

        function ger($table){
                return $this->getUserRight($table,'edit');
        }

        function gdr($table){
                return $this->getUserRight($table,'delete_');
        }
}  
