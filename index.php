<?PHP 

define( '_BASE_', 1 );

require_once( 'common/config.php' );
require_once( 'common/common.php' );
require_once( 'common/database.php' );
  
if(isset($conf_dbreadonly) && $conf_dbreadonly) die("DB in read-only mode");

$session = new session($database);
$user = new users($database);
$req = new request();


if(is_null($req->task)){ $req->task = 'view'; }
if(is_null($req->option)){ $req->option = 'mainpage';}

if(!$session->isSessionExists()){
 if(!is_null($req->usrname) && !is_null($req->pass) && $user->check($req->usrname,$req->pass)){
   $session->createSession($user);
   $user->setTypeName();
   $permissions = new rights($database,$user);
   $req->option = 'mainpage';
 }else{
   $req->option = 'login';
 }
}else{
   $user->load($session->userid);
   $session->updateSession();
   $permissions = new rights($database,$user);
   $user->setTypeName();
}

loadModule($req->option);
$viewClass = $req->option."View";
$view = new $viewClass();

$controllerClass = $req->option;
$controller = new $controllerClass($view,$req->task,$req->option,$database);
ob_start();
 $controller->run();
 $_TMPCNT = ob_get_contents();
ob_end_clean();

if(file_exists("tpl/".$req->option.".php"))  require_once( "tpl/".$req->option.".php" ); else  require_once( "tpl/index.php" );
