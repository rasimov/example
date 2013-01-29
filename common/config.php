<?php

$conf_offline = '0';
$conf_host = 'localhost';
$conf_user = 'root';
$conf_pass = '';
$conf_db = 'adm';
$conf_dbprefix = 'adm_';
$conf_lang = 'russian';
$conf_absolute_path = str_replace("\\common","",__DIR__);
$conf_arch = 'arch';
$conf_mysql_data_path = 'c:\\smartserver\\mysql\\data';
$conf_live_site = 'http://example.local/';
$conf_sitename = 'CMS';
$conf_debug = '1';
$conf_offset = '0';
$conf_mailer = 'smtp';
$conf_mailfrom = '';
$conf_mailto   = '';
$conf_fromname = '';
$conf_sendmail = '/usr/sbin/sendmail';
$conf_smtpauth = '1';
$conf_smtpuser = '';
$conf_smtppass = '';
$conf_smtphost = '';

///////  DB READ-ONLY MODE  ///////
//$conf_dbreadonly = true;
///////  USERS NOTICE       ///////
//$conf_usernotice = "";
///////  USE SECOND LOCALE  ///////

$conf_cachedir = $conf_absolute_path.'/cacheimg/';
$conf_cachetime = 3600; 

$conf_themephoto_abspath = $conf_absolute_path.'/img/photo/';
$conf_themephoto_relpath = $conf_live_site.'img/photo/';
$conf_themephotopreview_abspath = $conf_absolute_path.'/img/thumb/';
$conf_themephotopreview_relpath = $conf_live_site.'img/thumb/';
$conf_themephotosmall_abspath = $conf_absolute_path.'/img/small/';
$conf_themephotosmall_relpath = $conf_live_site.'img/small/';
$conf_thumbnail_big_abspath = $conf_absolute_path.'/img/thumbsbig/';
$conf_thumbnail_big_relpath = $conf_live_site.'img/thumbsbig/';
$conf_thumbnail_small_abspath = $conf_absolute_path.'/img/thumbssmall/';
$conf_thumbnail_small_relpath = $conf_live_site.'img/thumbssmall/';
$conf_infograph_abspath = $conf_absolute_path.'/img/infograph/';
$conf_infograph_relpath = $conf_live_site.'img/infograph/';
$conf_inline_abspath = $conf_absolute_path.'/img/text/';
$conf_inline_relpath = $conf_live_site.'img/text/';
$conf_inline_relpath = $conf_live_site.'img/text/';

//$conf_usememcache = false;
if(isset($conf_usememcache) && $conf_usememcache){
   $memcache = new Memcache;
   $memcache->connect('127.0.0.1', 11211) or die ("Could not connect memcache");
}
//  SPHINX 
$conf_usesphinx = true;
$conf_sphinx_host = '127.0.0.1';
$conf_sphinx_port = 9312;


$conf_logging_qry = false;
$confstorelog='d:/log/queries/';

$conf_use_locales = false;
$conf_admin_locales = false;

$conf_timeout_session = 30; // in minutes

session_name( md5( $conf_live_site ) );
session_start();

spl_autoload_register(function ($class) {
    global $conf_absolute_path;
    if(file_exists($conf_absolute_path.'/classes/ui/' . $class . '.php'))     include $conf_absolute_path.'/classes/ui/' . $class . '.php';
    if(file_exists($conf_absolute_path.'/classes/system/' . $class . '.php')) include $conf_absolute_path.'/classes/system/' . $class . '.php';
    if(file_exists($conf_absolute_path.'/classes/model/' . $class . '.php'))  include $conf_absolute_path.'/classes/model/' . $class . '.php';
});                 