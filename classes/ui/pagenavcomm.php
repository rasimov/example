<?php

defined( '_BASE_' ) or die;

class pagenavcomm{

var $pagelimit = null;
var $pagestart = null;
var $limitend  = null;
var $act       = null;
var $actprev   = null;
var $actnext   = null;
var $innername = null;
var $div       = null;

 function __construct($rowsnum,$task,$p = '#prev',$n = '#next',$r = '',$k='pagenavigator',$pagelimit=10){
  $this->limitend = $rowsnum;
  $this->div = $k;
  $this->pagelimit = $pagelimit;
  $this->act  = '';
  if(isset($_POST['task_nav'.$r])){
   if(!$this->act = $_POST['task_nav'.$r]){
    $this->act  = '';
   }                                
  }
  $this->actprev   = $p;
  $this->actnext   = $n;
  $this->innername = $r;

  if(!(isset($_SESSION['pagestart'.$this->innername]))){$_SESSION['pagestart'.$this->innername]=0;}else{
//   if($_SESSION['pagestart'.$this->innername]>$rowsnum){$_SESSION['pagestart'.$this->innername]=0;}
  }

 if(isset($_POST['pagestart'.$r])){
    $this->pagestart = $_POST['pagestart'.$r] ;
 }else
   if(isset($_SESSION['pagestart'.$r])){
    $this->pagestart = $_SESSION['pagestart'.$r];
   }else {
    $this->pagestart = 0;
 }
}

 function clear_count(){
   $_SESSION['pagestart'.$this->innername]=0;
   $this->pagestart=0;
 }

 function make_action(){
  switch($this->act){
   case $this->actprev:
    $this->prev();
   break;

   case $this->actnext:
    $this->next();
   break;
  } 
  $this->act = '';
  if(!(isset($_SESSION['pagestart'.$this->innername]))){$_SESSION['pagestart'.$this->innername]=0;}else{
     if($_SESSION['pagestart'.$this->innername]>$this->limitend){$_SESSION['pagestart'.$this->innername]=0;$this->pagestart=0;}
  }
 }

 function next(){
  if(($this->pagestart+$this->pagelimit) < $this->limitend){
  $r = $this->pagestart+$this->pagelimit;
  $this->pagestart=$r;
  }
 }

 function prev(){
  if(($this->pagestart-$this->pagelimit) >= 0){
  $r = $this->pagestart-$this->pagelimit;
  $this->pagestart=$r;
  }
 }

 function getlimit(){
 echo "<div id=\"".$this->div."\"><table>";
   $pg_cnt=round($this->pagestart/$this->pagelimit);
   $pg_num=0;
   $p1=$this->limitend;
   $p2=$this->pagelimit;
   $pg_num=$this->get_precig($p1,$p2);
   $pg_beg = 0; $pg_end = $pg_num;

   if(($pg_cnt-3)>0){$pg_beg = $pg_cnt-3;}else{$pg_beg = 0;}
   if(($pg_cnt+3)<$pg_num){$pg_end = $pg_cnt+3;}else{$pg_end = $pg_num;}
   if(($pg_cnt<5)and($pg_num>6)){$pg_beg = 0; $pg_end = 6;}
   if(($pg_cnt<$pg_num+1)and($pg_cnt>$pg_num-6)){$pg_beg = $pg_num-6; $pg_end = $pg_num;}
   if($pg_num<6){$pg_beg = 0; $pg_end = $pg_num;}
   if($pg_end>$pg_num){$pg_end = $pg_num;}

   $this->getstrcount($pg_beg,$pg_end);

   echo "</table></div>";
   $_SESSION['pagestart'.$this->innername]=$this->pagestart;

   ?>
      <script language="javascript" type="text/javascript">
          function navform<?php echo $this->innername;?>(pressbutton){
  	document.adminForm.task_nav<?php echo $this->innername;?>.value=pressbutton;
  	document.adminForm.submit();
          }
      
          function navstrform<?php echo $this->innername;?>(pressbutton){
  	document.adminForm.pagestart<?php echo $this->innername;?>.value=(pressbutton*1)*document.adminForm.pagelimit<?php echo $this->innername;?>.value;
  	document.adminForm.submit();
          }
     </script>
     <input type="hidden" name="pagestart<?php echo $this->innername;?>" value="<?php echo $this->pagestart;?>" />
     <input type="hidden" name="task_nav<?php echo $this->innername;?>" value="" />
     <input type="hidden" name="pagelimit<?php echo $this->innername;?>" value="<?php echo $this->pagelimit;?>" /><?php
 }

 function  getstrcount($beg,$end1){

  echo "<td class=\"title\">Pages:&nbsp</td>";
  if($beg>1){
    echo "<td class=\"title\">";
    $link = "javascript:navstrform".$this->innername."('0');";  
    echo "<a href= \"".$link."\" class=\"p11\">1-ÿ</a>"; 
    echo "</td>";
    echo "<td class=\"title\">.......</td>";

  }

  for ($i = $beg; $i < $end1; $i++){
   if($i!=$beg){ echo "<td class=\"title\">..</td>"; }
    echo "<td class=\"title\">";
    $link = "javascript:navstrform".$this->innername."('".$i."');";  
    if($i*$this->pagelimit == $this->pagestart){
       echo "<a href= \"".$link."\" class=\"p12\">".($i+1)."</a>"; 
    }else{
     echo "<a href= \"".$link."\" class=\"p11\">".($i+1)."</a>"; 
    }
    echo "</td>";
  }     

  $p1=$this->limitend;
  $p2=$this->pagelimit;
  $pg_num=$this->get_precig($p1,$p2);
  $link = "javascript:navstrform".$this->innername."('".($pg_num-1)."');";  
  echo "<td>.....<a href= \"".$link."\" class=\"p11\">last page</a></td>";
 }

 function get_precig($arg1,$arg2){
   $m=0;$y=$arg2;
   for($i=0;$i<=$arg1-1;$i=$i+$y){  $m=$m+1;}
   return($m);
 }
} 
