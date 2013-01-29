<?php
define( '_BASE_', 1 );

require_once( '../common/config.php' );
require_once( '../common/common.php' );
require_once( '../common/database.php' );
require_once( '../js/JsHttpRequest/JsHttpRequest.php');

$JsHttpRequest =& new JsHttpRequest("windows-1251");

$session = new session($database);
$user = new users($database);
$recipient = new users($database);
$chat = new chat($database);
$chatviewed = new chatviewed($database);

if($session->isSessionExists()){
   $user->load($session->userid);
   $session->updateSession();
   $chat = new chat($database);

   $mess = $chat->getPublicMessages($user->id);
   $messages="";
   if(count($mess)) foreach($mess as $m){
     $messages="<small>".date('H:i:s d.m.Y',strtotime($m->messtime))." ".$m->username.": ".$m->message."</small><br>".$messages;
   }

   $mess = $chat->getPrivateMessages($user->id);
   $privatemessages="";
   if(count($mess)) foreach($mess as $m){
     $privatemessages="<small>".date('H:i:s d.m.Y',strtotime($m->messtime))." ".$m->username." to ".$user->username.": ".$m->message."</small><br>".$messages;
   }

   $chat_id = $chatviewed->getUserMaxChatId($user->id);

   $allunread=$chat->countPublicUnread($chat_id,$user->id);

   $priunread=$chat->countPrivateUnread($chat_id,$user->id);
   
   $GLOBALS['_RESULT'] = array(  "resp"   => 'ok',
                                 "messages"=>$messages,
                                 "privatemessages"=>$privatemessages,
                                 "all"=>$allunread,
                                 "pri"=>$priunread);
}else{
   $GLOBALS['_RESULT'] = array(  "resp"   => 'bad',);
}
