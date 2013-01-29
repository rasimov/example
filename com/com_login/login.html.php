<?php 
defined( '_BASE_' ) or die;

class loginView extends view{

 function render_view(){

   global $conf_live_site,$database;
   $session = new session($database);
   if($session->isSessionExists()){ header("Location: /mainpage/"); die; }

 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<meta http-equiv="Content-Language" content="ru" />
<title>Login page</title>
<style type="text/css">
@import url(<?php echo $conf_live_site;?>css/login.css);
</style>
<script type="text/javascript" src="<?php echo $conf_live_site;?>js/jquery.js"></script>
<script> var $jQ=jQuery.noConflict();</script> 
<script language="javascript" type="text/javascript">
	function setFocus() {
		document.loginForm.usrname.select();
		document.loginForm.usrname.focus();
	}
</script>
</head>
<body onload="setFocus();">

<form action="/" method="post" name="loginForm" id="loginForm" style="border:0px solid red;text-align:center;">
<div class="authwin"  align="right">
  <table cellspacing=0 cellpadding=0>
    <tr>
      <td class="datahead">
        <table cellspacing=0 cellpadding=0><td><span>Administration module</span></td><td width="1%"><img id="hideauth4" src="<?php echo $conf_live_site?>img/spc.gif" hspace="10"/></td></table>
      </td>
    </tr>
    <tr>
      <td>
        <div class="authcont"> 
          <table align="center" border=0 cellpadding=0>
           <tr> 
            <td width="110px"> 
             <img src="<?php echo $conf_live_site?>img/key.gif" vspace="10px" width="100px"/>
            </td>
            <td  class="auth_priv" colspan="2" style="text-align:center;" > &nbsp;
            </td>
           </tr> 
           <tr> 
            <td class="alreadyauth" colspan=3>  
             Please enter your login and password
            </td> 
           </tr> 
           <tr> 
            <td class="alreadyauth" style="height:40px;">Login</td> 
            <td class="loginpassw" colspan=2 style="vertical-align:middle;">  
             <input name="usrname" value='zerg'/>
            </td> 
           </tr> 
           <tr> 
            <td class="alreadyauth" style="height:40px;">Password <small>nfnmzyfdscjwrfz<small></td> 
            <td class="loginpassw" colspan=2 style="vertical-align:middle;">  
             <input name="pass" type="password" value=''/> &nbsp;<input type="image" src="<?php echo $conf_live_site?>img/login.gif" name="submit" value="1" align="absmiddle" style="width:22px;border:none;"border=0>
             <input type="hidden" name="sub" value="auth"/>
            </td> 
           </tr> 
          </table>
        </div>
      </td>
    </tr>
  </table>
</div>
</form>

<noscript>
Please enable Javascript
</noscript>

</body>
</html>
<?php
 }
}