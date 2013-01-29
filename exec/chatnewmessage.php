<?php
define( '_BASE_', 1 );

require_once( '../common/config.php' );
require_once( '../common/common.php' );
require_once( '../common/database.php' );
require_once( '../js/JsHttpRequest/JsHttpRequest.php');


$JsHttpRequest =& new JsHttpRequest("windows-1251");

$session = new session($database);
$user = new users($database);
$receiver = new users($database);
$chat = new chat($database);
$chatviewed = new chatviewed($database);

if($session->isSessionExists()){
   $user->load($session->userid);
   $session->updateSession();
   $chat->message=htmlspecialchars($_POST['message']);
   $chat->sender_id = $user->id;
   if(strpos($chat->message,"-")!==false){
     $receiver->findUser(trim(substr($message,0,strpos($chat->message,"-")-1)));
     if(!is_null($receiver->id)){
         $chat->message=trim(substr($chat->message,strpos($chat->message,"-")+1));
         $chat->receiver_id = $receiver->id;
     }
   }
   $chat->store(); 
   $GLOBALS['_RESULT'] = array(  "resp"   => 'ok');
}else{
   $GLOBALS['_RESULT'] = array(  "resp"   => 'bad');
}
