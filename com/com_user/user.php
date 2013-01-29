<?php 

defined( '_BASE_' ) or die;

class user extends controller{

         var $usertypes =  null;

         function __construct(&$view,$task,$option,&$database){
           $this->init(&$view,new users($database),$task,$option);
           $this->view->setName(array('Users','user'));
           $this->usertypes = new usertypes($database);
         }  

         function view(){
           $pgnav = new pagenavcomm(0,$this->task,"#prev_".__CLASS__,"#next_".__CLASS__,__CLASS__,"pagnavcat");
           $pgnav->limitend = count($this->model->getCount($this->req->search));
           $pgnav->make_action();
           $rows = $this->model->getList($this->req->search,array("limit"=>$pgnav->pagestart.",".$pgnav->pagelimit));
           $buttons = new buttons(array("add","edit","delete","back"));
           $this->view->render_view((object)array("rows"=>$rows,"pgnav"=>$pgnav,"buttons"=>$buttons));
         }

         function add(){
           $this->model->reset();
           $buttons = new buttons(array("save","view"));
           $this->view->render_add(
               (object)array(
                              "obj"=>$this->model,
                              "buttons"=>$buttons,
                              "usertypes"=>$this->usertypes->loadList(array("1"=>"1"))
                             )
           );
         }

         function edit(){
           $this->model->load($this->req->cid[0]);
           $buttons = new buttons(array("save","view"));
           $this->view->render_edit((object)array("obj"=>$this->model,"buttons"=>$buttons,"usertypes"=>$this->usertypes->loadList(array("1"=>"1"))));
         }

         function save(){
           $this->model->bind($this->req->all());
           $this->model->password=md5($this->model->password);
           $this->model->store();
           $this->view();
         }

         function delete(){
           if(count($this->req->cid))foreach($this->req->cid as $id){
              $this->model->delete($id);
           }
           $this->view();
         }

         function back(){
           header('Location: /');
         }
}

?>
