<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Career</title>

<script type="text/javascript" src="11-2-routing.js"></script>
<style type="text/css">
<!--

.STYLE1 {
	color: #FF0000;
	font-weight: bold;
}
.STYLE2 {
	color: #000000;
	font-weight: bold;
}

#mainContent{width: 300px; height:200px; padding:20px;} .nav{margin-bottom: 15px; text-decoration:none;} #nav li{display:inline; list-style:none; border:1px solid black; padding:3px 15px; #nav a {text-decoration: none;}
-->
</style>
</head>

	
<body>

<table width="981" height="939" border="0" align="center" bgcolor="#CCCCCC" matgin: 0 auto;>
  <tr>
    <td width="94" rowspan="3" valign="top" bordercolor="#CCCCCC" bgcolor="#CCCCCC"><table width="200" border="0">
      <tr id="nav">
        <td height="64" align="center" valign="middle"><a href="index.php?home"><img src="img/pic1.jpg"></a></td>
      </tr>
      <tr>
        <td height="68" align="center" valign="middle"><a href="index.php?products"><img src="img/pic2.jpg"></a></td>
      </tr>
      <tr>
        <td height="57" align="center" valign="middle"><a href="index.php?about"><img src="img/pic3.jpg"></a></td>
      </tr>
    </table>
    <p class="STYLE1">&nbsp;	</p>
      <p class="STYLE1">&nbsp; </p>
      <p>&nbsp;</p>
    <p>&nbsp;</p>      <p>&nbsp;</p></td>
    <td height="92" bordercolor="#CCCCCC" bgcolor="#CCCCCC"><p class="STYLE1">Career</p>
      <p><strong>Seach：
        <input type="text" name="neirong"/>
            <input name="submit" type="submit" value="提交搜索" />
    </strong></p></td>
  </tr>
  <tr>
    <td width="871" height="676" valign="top" bgcolor="#CCCCCC">
	
	<div id="output">
	<?php

require('conn.php');



$PageSize=10;
$act=$_GET["action"];
if($act=="search")
{
   $type=$_POST["type"];
   $keywords=$_POST["keywords"];
   if($_SESSION['id']!=="" ){
  $sql="select * from  zhaoadd order by id desc";
}
$result=mysql_query($sql)  or die("无法执行SQL：$sql");
$amount=mysql_num_rows($result);
}
if(isset($_GET["page"]))
{
$page=(int)$_GET["page"];
}
else
{

$page=1;

}
if($amount){

if ($amount<$PageSize){

$PageCount=1;
}
if($amount%$PageSize){

$PageCount= $amount/$PageSize+1;

$PageCount=intval($PageCount);
}
else
{
$PageCount=$amount/$PageSize;
}
}

else{

$PageCount=0;
}




//翻页连接

$Page_String="";
if ($page==1){
$Page_String.='First page|Previous Page|';
}
else{

$Page_String.='<a href=?page=1>First Page</a>|<a href=?page='.($page-1).'>Previous Page</a>|';

}

if(($page==$PageCount)||($PageCount==0)){


$Page_String.='Next Page|Last Page|';

}
else{

$Page_String.='<a href=?page='.($page+1).'>Next Page</a>|<a href=?page='.$PageCount.'>Last Page</a>';

}mount-($page-1)*$PageSize;
$num=$a


?>
<?php 


 $upLimit   =   ($page-1)*$PageSize;   
  $lowLimit   =  $PageSize; 
  $act=$_GET["action"];
if($act=="search")
{
   $type=$_POST["type"];
   $keywords=$_POST["keywords"];
  $sql="select * from  zhaoadd where ".$type." ='".$keywords."' order by id desc  limit ".$upLimit ."  ,".$lowLimit."";
}
else
{
   $sql="select * from  zhaoadd  order by id desc  limit ".$upLimit ."  ,".$lowLimit." ";
}
$result=mysql_query($sql)  or die("无法执行SQL：$sql");


while($rs=mysql_fetch_object($result))
{


?> 

	<table  width="863" border="0">
        <tr>
          <td colspan="2"><span class="STYLE1"><?php echo $rs->zhiwei;?></span> (<?php echo $rs->qixian;?>)</td>
        </tr>
        <tr>
          <td width="109">Number</td>
          <td width="738" align="left"><?php echo $rs->renshu;?>&nbsp;</td>
        </tr>
        <tr>
          <td><p>Requirtment:</p></td>
          <td width="738" align="left"><?php echo $rs->yaoqiu;?>&nbsp;</td>
	  
        </tr>
      </table>
	  	  

	<?php
}
 ?>  
	 </div>   
      </td>
  </tr>
  <tr>
    <td height="139" font-size:"20" bgcolor="#CCCCCC"><div  id="output"><a href="xiangxi.php" class="STYLE2">点击申请加入>></a></div></td>
  </tr>
  
   
</table>	
<p>&nbsp;</p>
    
		  		</div>
	</div>

</body>
</html>
