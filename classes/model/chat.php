<?php

defined( '_BASE_' ) or die;

class chat extends DBTable {
        var $id                     = null;
        var $sender_id              = null;
        var $receiver_id            = null;
        var $messtime               = null;
        var $message                = null;
        var $lastvisit              = null;
        var $viewed                 = null;

        function __construct( &$database ) {
                $this->DBTable( '#__chat', 'id', $database );
        }

        function getPublicMessages($user_id){
           $mess = $this->loadList(array("sender_id"=>$user_id,"receiver_id"=>array("is"=>"null")),
                                   array("join"=>array("i"=>"#__users","on"=>array("sender_id"=>array("="=>"#__users.id"))),
                                         "orderby"=>array("messtime"=>"desc"),
                                         "limit"=>200)
                                   );
              return($mess);
        }
        function getPrivateMessages($user_id){
              $mess = $this->loadList(
		array(
			"receiver_id" => $user_id,
			"sender_id" => array("is not"=>"null")
		),
                array(
		       "join" => array(
				"i" => "#__users",
				"on" => array(
					"receiver_id" => array("="=>"#__users.id")
				)
			),
                        "orderby"=>array("messtime"=>"desc"),
                       	"limit"=>200)
               );
              return($mess);
        }
        
        function countPublicUnread($chat_id,$user_id){
              return($this->count(array('receiver_id'=>array('is'=>'null'),'sender_id'=>array("<>"=>$user_id),"id"=>array(">"=>$chat_id))));
        }

        function countPrivateUnread($chat_id,$user_id){
              return($this->count(array('receiver_id'=>array('='=>$user_id),"id"=>array(">"=>$chat_id))));
        }
}  
