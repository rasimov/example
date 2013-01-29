<?php 
defined( '_BASE_' ) or die( 'Доступ запрещен' );

class userView extends view{

  function render_view($data){
    $rows = $data->rows;
    $pgnav = $data->pgnav;
    print($data->buttons->show($this->name));
?> <form action="/user/" method="post" name="adminForm" onsubmit="return false;">
    <table class="adminlist">
     <tr>
       <th width="2%" class="title">#</th>
       <th width="2%" class="title">
        <input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($rows); ?>);" />
       </th>
       <th class="title" >Login</th>
       <th class="title" >Usrname</th>
       <th class="title" >Role</th>
       <th class="title" >ID</th>
     </tr>
 <?php
  for ($i = 0,$n = count($rows); $i < $n ; $i++){
    $row = & $rows[$i];
   ?>
    <tr>
     <td><?php echo $i+1;?></td>
     <td><input type="checkbox" id="cb<?php echo $i;?>" value="<?php echo $row->id;?>" name="cid[]" onclick="isChecked(this.checked);" /></td>
     <td><?php echo$row->name;?></td>
     <td><?php echo$row->username;?></td>
     <td><?php echo$row->typename;?></td>
     <td><?php echo $row->id;?></td>
    </tr>
<?php }?>
    </table>
<?php $pgnav->getlimit(); ?>
    <input type="hidden" name="task" value="<?php echo $task;?>" />
    <input type="hidden" name="boxchecked" value="0" />
  </form>
   <?php
  }


function render_add($data){
  print($data->buttons->show('Add user'));
  $this->render_modify($data);
}

function render_edit($data){
  print($data->buttons->show('Edit user'));
  $this->render_modify($data);
  if(is_null($data->obj->id)){
     ?><script>alert('Please choose user to edit');document.adminForm.submit();</script><?php
  }
}

function render_modify($data){
?>
<script>
function submitbutton(pressbutton) {

var form = document.adminForm;
        if ((pressbutton == 'back')||(pressbutton == 'view')) {
                submitform( pressbutton );
                return;
        }
        if (trim(form.name.value) == "") {
                alert( "Enter login." );
        } else if (form.username.value == "") {
                alert( "Enter username." );
        } else if (form.name.value.length < 3) {
                alert( "Login too short." );
        } else if (trim(form.password.value) != "" && form.password.value != form.password2.value){
                alert( "Passwords don't equal." );
        } else {
                submitform( pressbutton );
        }
}

</script>

<form action="/user/" method="post" name="adminForm" enctype="multipart/form-data">
      <table class="adminlist" border=0 >
                                <tr>
                                        <td width="100">
                                        Login:
                                        </td>
                                        <td width="85%">
                                        <input type="text" name="name" class="inputbox" size="40" value="<?php echo $data->obj->name;?>" />
                                        </td>
                                </tr>
                                <tr>
                                        <td>
                                        Username:
                                        </td>
                                        <td>
                                        <input type="text" name="username" class="inputbox" size="40" value="<?php echo $data->obj->username;?>" />
                                        </td>
                                </tr>
                                <tr>
                                        <td>
                                        Role:
                                        </td>
                                        <td>
                                         <select name = "usertype">
                                         <?php 
                                          foreach($data->usertypes as $m){
                                           if($m->usertype == $data->obj->usertype) {
                                            echo "<option value =\"$m->usertype\"selected> $m->typename </option>";
                                           } else {
                                            echo "<option value =\"$m->usertype\"> $m->typename </option>";
                                           }

                                          };
                                         ?>
                                         </select>
                                        </td>
                                </tr>
                                <tr>
                                        <td>
                                        Password:
                                        </td>
                                        <td>
                                        <input type="password" name="password" class="inputbox" size="40" value="" />
                                        </td>
                                </tr>
                                <tr>
                                        <td>
                                        Retype password:
                                        </td>
                                        <td>
                                        <input type="password" name="password2" class="inputbox" size="40" value="" />
                                        </td>
                                </tr>
    </table>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="id" value="<?php echo $data->obj->id;?>" />
  </form>
<?php
}

}
?>