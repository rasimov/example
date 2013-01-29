<?php 

defined( '_BASE_' ) or die;

class logout extends controller{

         function __construct(&$view,$task,$option,&$database){
           $session = new session($database);
           $session->dropSession();
           $this->init(&$view,$this->model,$task,$option);
         }  

         function view(){
            $this->view->render_view();
         }
}