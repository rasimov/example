<?php

defined( '_BASE_' ) or die;

class session extends DBTable {
        var $id                     = null;
        var $username               = null;
        var $time                   = null;
        var $session_id             = null;
        var $userid                 = null;
        var $usertype               = null;

        function __construct( &$database ) {
                $this->DBTable( '#__session', 'id', $database );
        }
        function isSessionExists(){
           if(isset($_SESSION['sessionid'])){ 
             $this->set('session_id',$_SESSION['sessionid']);
             $this->_tbl_key = 'session_id';
             $this->load(); 
             return is_null($this->id)?false:true;
           } 
           return false;
        }

        function createSession($user){
           $logintime = time();
           $this->_tbl_key = 'id';
           $this->session_id = md5( $user->id . $user->username . $user->usertype . $logintime );
           $this->username = $user->username;
           $this->usertype = $user->usertype;
           $this->time = $logintime;
           $this->userid = $user->id;
           if($this->store()) {
             $_SESSION['sessionid'] = $this->session_id;
             return true;
           }else{
             return false;
           }
        }
        function updateSession(){
           global $conf_timeout_session;
           $past = time() - $conf_timeout_session*60;
           $this->_db->setQuery( "DELETE FROM ".$this->_tbl." where time < '$past'" );
           $this->_db->query();
           $this->time=time();
           $this->store();
        }

        function dropSession(){
           if(isset($_SESSION['sessionid'])){ 
             $this->set('session_id',$_SESSION['sessionid']);
             $this->_tbl_key = 'session_id';
             $this->load(); 
             $this->delete($this->id);
             session_destroy();
             header('Location: /');
           } 
        }
}  
