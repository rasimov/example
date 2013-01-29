<?php
define( '_BASE_', 1 );

require_once( '../common/config.php' );
require_once( '../common/common.php' );
require_once( '../common/database.php' );
require_once( '../js/JsHttpRequest/JsHttpRequest.php');


$JsHttpRequest =& new JsHttpRequest("windows-1251");

$user = new users($database);
$chat = new chat($database);
$chatviewed = new chatviewed($database);
$session = new session($database);

if($session->isSessionExists()){
   $user->load($session->userid);
   $session->updateSession();
   $chatviewed->updateChat($user->id,$chat->max(array('1'=>'1')));
   $GLOBALS['_RESULT'] = array(  "resp"   => 'ok',);
}else{
   $GLOBALS['_RESULT'] = array(  "resp"   => 'bad',);
}
