<?php

defined( '_BASE_' ) or die;

class buttons {
        var $buttons_preset=array(
           "add"    => array("add_diag.png"   ,"Add"),
           "edit"   => array("edit_diag.png"  ,"Edit"),
           "delete" => array("remove_diag.png","Delete"),
           "save"   => array("applay_diag.png","Save"),
           "back"   => array("back_diag.png"  ,"Back"),
           "view"   => array("back_diag.png"  ,"Back")
        );
        var $buttons = null;

        function __construct($buttons) { 
           $this->buttons = $buttons;
        }

        function show($name=""){
           $bt_html ="<table id = \"toolbar\" border=1><td width=\"20%\" style=\"text-align:center;\">";

             foreach($this->buttons as $btn){
                 $bt_html.=$this->quickiconButton( $btn, $this->buttons_preset[$btn][0], $this->buttons_preset[$btn][1], 34 );
             }
           $bt_html.='<table border=0><th class="user" WIDTH="500px" style="text-align:center;"><b class="headname">'.$name.'</b></th></table>';
           $bt_html.="</td></table>";
           return $bt_html;
        }

        function quickiconButton( $action, $image, $text, $size = null ) {
          $link = "javascript:submitbutton('".$action."');";
          $tmp='<div style="float:left;padding-top:0px;"><div class="icon">';
             $mousover = "return overlib('". $text ."', BELOW, RIGHT );";
             $style = 'style="text-decoration: none; color: #333;"';
             $tmp.= '<a href= "'.$link.'" onmouseover="'. $mousover .'" onmouseout="return nd();" '.$style .'>'. 
                       '<img src="/img/'. $image .'" align="middle" border="0" height = "'. $size .'" width = "'. $size .'" />' .
                     '</a>';
             $tmp.='</a></div></div>';
          return $tmp;
        }
}  
