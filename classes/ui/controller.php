<?php
defined( '_BASE_' ) or die;

class controller {
        var $view                   = null;
        var $task                   = null;
        var $model                  = null;
        var $option                 = null;
        var $req                    = null;
        function __construct(){}

        function init(&$view,&$model,$task,$option) {
            $this->model = &$model;                 
            $this->view  = &$view;                 
            $this->option = $option;
            $this->task = $task;
            $this->req  = new request();
        }
        function view(){

        }
        function edit(){

        }
        function add(){

        }
        function back(){

        }
        function delete(){

        }
        function save(){

        }
        function run(){
          $task = $this->task; 
          if(method_exists($this,$task)){
            $this->$task();
          }else{
            $this->view();
          }  
        }
}  
