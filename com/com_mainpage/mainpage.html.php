<?php 
defined( '_BASE_' ) or die;

class mainpageView extends view{

 function render_view(){
 ?> <table id="mainpagetable">
     <td width="2px"><img src="<?php echo $conf_live_site?>img/spc.gif" width="2px" height="600px"></td>
    </table><?php
 }

}