<?php
defined( '_BASE_' ) or die;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo $conf_sitename; ?> </title>
<style type="text/css">
@import url(/css/template_css.css);
@import url(/css/theme.css);
</style>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<meta http-equiv="Content-Language" content="ru" />
<script language="JavaScript" src="/js/JSCookMenu_mini.js" type="text/javascript"></script>
<script language="JavaScript" src="/js/ThemeOffice/theme.js" type="text/javascript"></script>
<script language="javascript" type="text/javascript" src="/js/JsHttpRequest/JsHttpRequest.js"></script>
<script type="text/javascript" src="/js/jquery.js"></script>
<script> var $jQ=jQuery.noConflict();</script> 
<script type="text/javascript" src="/js/prototype.js"></script>
<script type="text/javascript" src="/js/resize.js"></script>
<script>var session_id = '<?php echo $session->session_id?>'</script>
<meta http-equiv="Pragma" content="no-cache" />
</head>
<body>
 <img src="/img/toolbar.jpg" id="topmenuimg" align="top">
 <table class="menubar"  cellpadding=0 cellspacing=0 border=0>
      <tr>
        <td style="padding-left:5px;">
            <?php LoadAdminModule( 'menu' );?>
        </td>
        <td>
         <div id="chatcount">
           Chat <small>(click to send)</small>: 
            <font style="color:red;">Private:<b id="privatecount">0</b></font> 
            <font style="color:blue">Public:<b id="publiccount">0</b></font>
         </div>
        </td>
        <td  align="right" style="padding-right:15px;">
            <b style="color:blue;"> User: </b>
            <b style="color:red;"> <?php echo $user->username;?> </b> 
            <b style="color:blue;"> role: </b> 
            <b style="color:red;"><?php echo $user->_typename;?></b>    
        <div style="font:12px;position:relative;float:right;">
        &nbsp;
         <div id="chat">
           <b class="marr40">Public:</b><a href="#" id="activateChat">Close</a><br>
           <div id="chatmsg"></div>
           <b>Private:</b>
           <div id="chatprivmsg"></div>
            <small>Send message (Enter to send), Private message:&lt;Part of the username&gt; space &lt;message&gt;</small><br>
           <textarea id="chatmessage"></textarea>
         </div>
        </div>
        </td>
      </tr>   
</table>
 <table class="adminform" width="100%">
   <tr>
     <td width="100%" valign="left">
       <?php echo $_TMPCNT; ?>
     </td>
   </tr>
 </table>
<script language="JavaScript" src="/js/jrt.js" type="text/javascript"></script>
</body>
</html>
