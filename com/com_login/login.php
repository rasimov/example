<?php 

defined( '_BASE_' ) or die;

class login extends controller{

         function __construct(&$view,$task,$option){
           $this->model = null;
           $this->init(&$view,$this->model,$task,$option);
         }  

         function view(){
            $this->view->render_view();
         }
}