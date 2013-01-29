<?php

function LoadAdminModule( $name, $params=NULL ) {
        global $conf_absolute_path;
        $path = "mod/mod_$name.php";
        if (file_exists( $path )) {
                require $path;
        }else{
                die;
        }
}

function loadModule($option){

  $modulePath = "com/com_";

  $controllerPath = $modulePath.$option."/".$option.".php";

  $viewPath       = $modulePath.$option."/".$option.".html.php";

  if ( !file_exists($controllerPath) || !file_exists($viewPath)) {
      header("Location: /");      
      die($controllerPath);
  }

  require_once($viewPath);
  require_once($controllerPath);
}
  
