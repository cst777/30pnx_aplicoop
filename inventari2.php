<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) 
{

$user = $_SESSION['user'];

?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>inventari ::: la coope</title>
		<style type="text/css">
	a#color:link, a#color:visited {color:white; border: 1px solid #9cff00;}
	a#color:hover {color:black; border: 1px solid #9cff00;   -moz-border-radius: 10%;}
   a#color:active {color:white; border: 1px solid #9cff00;  -moz-border-radius: 10%;}
		</style>
</head>
<body>

<div class="pagina" style="margin-top: 10px;">

<div class="contenidor_1" style="border: 1px solid black;">
<p class='path'> 
><a href='admint.php'>administració</a> 
 >><a href='inventari2.php'>veure estoc actual</a> </p>
 
<p class="h1" style="background: black; text-align: left; padding-left: 20px;">
Veure estoc actual</p>

<div class="cat" style="width: 750px; margin-left: auto; margin-right: auto;">
<?php

include 'config/configuracio.php';
$color=array("#F0C900","#00b2ff","orange","#b20000","#14e500","red","#8524ba");
$cc=0;
/*
Commented by 30Panxes - December 2016
$sel = "SELECT nom FROM proveidores ORDER BY nom";
$result = mysql_query($sel);
if (!$result) { die('Invalid query: ' . mysql_error());  }
while ($nomprov = mysql_fetch_array($result))
{
	print ('<a id="color" href="#'.$nomprov[0].'" 
		style="background: '.$color[$cc].'; margin-bottom: 5px; margin-right: 3px; 
		white-space: -moz-pre-wrap; word-wrap: break-word;">
		<span>'.$nomprov[0].'</span></a>');
		$cc++;
		if ($cc==7){$cc=0;}
}
End Commented by 30Panxes - December 2016
*/
?>

</div>	
<div class="contenidor_fac" style="border: 1px solid black; height: 350px; overflow: scroll;
 margin-bottom: 20px;">

<?php
$cc=0; $comp=""; $nova="";
/* Modified by 30Panxes (December 2016)
$sel2 = "SELECT nom, unitat, proveidora, estoc FROM productes ORDER BY proveidora,nom";
*/
$sel2 = "SELECT nom, unitat, proveidora, estoc FROM productes WHERE estoc <> 0 ORDER BY proveidora,nom";
$result2 = mysql_query($sel2);
if (!$result2) {die('Invalid query result2: ' . mysql_error());}

while (list($prod,$unitat,$prov,$estoc)= mysql_fetch_array($result2))
{
	if ($estoc<=0){$class='style="color: red;"';}
	else {$class='';}
	
	

	if ($comp!=$prov)
	{
		if($nova==0){ print ('</table>');}
		print ('<a name="'.$prov.'"></a> 
		<p class="h1"
		style="background: '.$color[$cc].'; font-size:14px; text-align: left; 
		height: 20px; padding-left: 20px; clear: left;">
		'.$prov.'</p>');
		
		$cc++;
		if ($cc==7){$cc=0;}

		echo '<table width="100%" align="center" valign="middle" cellpadding="5" cellspacing="5">';
		echo "<tr class='cos'>";
		print('<td '.$class.'>'.$prod.': '.$estoc.' '.$unitat.'</td></tr>');
		$nova=1;
	}
	
	else
	{
		print('<tr class="cos"><td '.$class.'>'.$prod.': '.$estoc.' '.$unitat.'</td></tr>');
		$nova=0;
	}

	$comp=$prov;
} 
	print ('</table>');


?>

</div>
</div>
</div>
</body>
</html>

<?php
include 'config/disconect.php';
} 
else {
header("Location: index.php"); 
}
?>