<?php
defined( '_BASE_' ) or die;
global $user,$permissions;

?>
<div id="m"></div>
<script language="JavaScript" type="text/javascript">
  var myMenu = [  
                ['','<b>Dictionary</b>',null,null,'',
                  ['<img src="/img/catalog1.gif" width="30" />','Tags','/tags/',null,''],                
                ],
                _cmSplit,_cmSplit,
                <?php if($user->getaccess()>1){?>
                ['','<b>Administration</b>',null,null,'',
                  ['<img src="/img/peoples.gif" width="30" />','Users','/user/',null,''],
                ],
                _cmSplit,_cmSplit,
                <?php } ?>
                ['','<b>Logout</b>','/logout/',null,'',]
               ];
 cmDraw ('m', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
</script>
