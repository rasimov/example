<?php

defined( '_BASE_' ) or die;

class chatviewed extends DBTable {
        var $id                     = null;
        var $user_id                = null;
        var $chat_id                = null;

        function __construct( &$database ) {
                $this->DBTable( '#__chatviewed', 'id', $database );
        }

        function updateChat($user_id,$chat_id){
                $this->load(array('user_id'=>$user_id));
                $this->user_id = $user_id;
                $this->chat_id = $chat_id;
                $this->store();
        }

        function getUserMaxChatId($user_id){
                $this->load(array('user_id'=>$user_id));
                return($this->chat_id);
        }
}  
