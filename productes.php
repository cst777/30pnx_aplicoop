<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

$pcat=$_POST['cat'];
$psubcat=$_POST['subcat'];
$pprov=$_POST['prov'];

include 'config/configuracio.php';
?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>productes ::: la coope</title>
	</head>

<style type="text/css">
   a#color:link, a#color:active {color:black;}
   a#color:hover {color:green; border: 1px solid #9cff00; -moz-border-radius: 10%;}
   a#color:visited {color:green;}
		</style>


<body>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid #990000;">
<p class='path'> 
><a href='admint.php'>administració</a> 
 >><a href='productes.php'>editar, crear, eliminar productes</a> 
 </p>
<p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">
Productes
<span style="display: inline; float: right; text-align: center; vertical-align: middle; 
padding: 2px 50px 2px 0px;">
<input class="button2" style="width:150px;" type="button" value="CREAR NOU PRODUCTE" onClick="javascript:window.location = 'editprod.php'">
</span>
</p>


<table width="80%" align="center">
<form action="productes.php" method="post" name="prod" id="prod">
<tr style="padding-top: 10px;" class="cos_majus">
<td width="33%" align="center" >Categories</td>
<td width="33%" align="center" >Subcategories</td>
<td width="33%" align="center" >Proveidores</td>
</tr>

<tr style="padding-bottom: 10px;">
<td align="center">

<SELECT name="cat" id="cat" size="1" maxlength="30" onChange="this.form.submit()">
<option value="">elegeix una categoria</option>

<?php

$select2= "SELECT tipus FROM categoria ORDER BY tipus";
$query2=mysql_query($select2);
if (!$query2) {die('Invalid query2: ' . mysql_error());}

while (list($scat)=mysql_fetch_row($query2)) 
{
if ($pcat==$scat)
	{
	echo '<option value="'.$scat.'" selected>'.$scat.'</option>';
	}
else
	{
	echo '<option value="'.$scat.'">'.$scat.'</option>';
	}
}
?>
</select>
</td>

<?php
$dis_sc="disabled";
$opt_sc='<OPTION value="">elegeix una categoria</option>';
if ($pcat!="") 
{
	$dis_sc="";
	$opt_sc='<OPTION value="">';
}

?>

<td align="center">
<SELECT name="subcat" id="subcat" size="1" maxlength="30" <?php echo $dis_sc; ?> onChange="this.form.submit()">

<?php
echo $opt_sc;
if ($pcat!="")
{
	$select2= "SELECT subcategoria FROM subcategoria 
	WHERE categoria='".$pcat."' ORDER BY subcategoria";
	$query2=mysql_query($select2);
	if (!$query2) {die('Invalid query2: ' . mysql_error());}

	while (list($scat)=mysql_fetch_row($query2)) 
	{
	if ($psubcat==$scat){echo '<option value="'.$scat.'" selected>'.$scat.'</option>';}
	else {echo '<option value="'.$scat.'">'.$scat.'</option>';}
	}
}
?>
</td>

<td align="center">
<SELECT name="prov" id="prov" size="1" maxlength="30" onChange="this.form.submit()">
<option value="">elegeix una proveïdora</option>

<?php
$select3= "SELECT nom FROM proveidores ORDER BY nom";
$query3=mysql_query($select3);
if (!$query3) {die('Invalid query3: ' . mysql_error());}

while (list($sprov)=mysql_fetch_row($query3)) 
{
	if ($pprov==$sprov){echo '<option value="'.$sprov.'" selected>'.$sprov.'</option>';}
	else {echo '<option value="'.$sprov.'">'.$sprov.'</option>';}
	
}
?>
</td>
</form>
</tr></table>

<div class="contenidor_fac" style="border: 1px solid #990000; max-height: 350px; overflow: scroll; overflow-x: hidden; 
margin-bottom: 20px; padding-bottom: 20px;">


<?php
if ($pcat!="" OR $pprov!="")
{
	if ($pcat=="") {$where="proveidora='".$pprov."'"; $title="Recerca per proveïdora ".$pprov;}
	else 
	{
		if ($psubcat=="" AND $pprov=="") {$where="categoria='".$pcat."'"; $title="Recerca per categoria ".$pcat;}
		elseif ($psubcat!="" AND $pprov=="") {$where="categoria='".$pcat."' AND subcategoria='".$psubcat."'"; $title="Recerca per categoria ".$pcat." i subcategoria ".$psubcat;}
		elseif ($psubcat=="" AND $pprov!="") {$where="categoria='".$pcat."' AND proveidora='".$pprov."'"; $title="Recerca per categoria ".$pcat." i proveïdora ".$pprov;}
		elseif ($psubcat!="" AND $pprov!="") {$where="categoria='".$pcat."' AND subcategoria='".$psubcat."' AND proveidora='".$pprov."'"; $title="Recerca per categoria ".$pcat.", subcategoria ".$psubcat." i proveïdora ".$pprov;}
	}
	
	print ('<p class="h1" 
		style="background: #990000; font-size:14px; text-align: left; 
		height: 20px; padding-left: 20px;">'.$title.'</p>');
	 
	print('<table width="80%" align="center" style="padding:0px;" >');
		
	$sel = "SELECT ref,nom,proveidora FROM productes 
	WHERE ".$where." ORDER BY nom";
	$result = mysql_query($sel);
	if (!$result) {die('Invalid query: ' . mysql_error());}
	
	$i=0;
	while (list($ref,$nomprod,$nomprov)= mysql_fetch_row($result))
	{	
	if ($i==0) {print ('<tr class="cos" cellspading="5" cellspacing="5">');}
	print('<td align="center"><a id="color" href="editprod.php?id='.$ref.'">'.$nomprod.'</a></td>');
	$i++;
	if ($i==3) {print ('</tr>'); $i=0;}
	} 
	print ('</table></div>');

}
else 
{
	print ('<p class="h1" 
		style="background: #990000; font-size:14px; text-align: left; 
		height: 20px; padding-left: 20px;">Ordenació alfabètica de productes</p>');
	 
	 print('<table width="80%" align="center">');
	
	$sel = "SELECT ref,nom,proveidora FROM productes ORDER BY nom";
	$result = mysql_query($sel);
	if (!$result) {die('Invalid query: ' . mysql_error());}
	
	$i=0;
	while (list($ref,$nomprod,$nomprov)= mysql_fetch_row($result))
	{	
	if ($i==0) {print ('<tr class="cos" cellspading="5" cellspacing="5">');}
	print('<td align="center"><a id="color" href="editprod.php?id='.$ref.'">'.$nomprod.'</a></td>');
	$i++;
	if ($i==3) {print ('</tr>'); $i=0;}
	} 
	print ('</table></div>');
}

?>

<p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
Per crear un nou producte clica el botó CREAR NOU PRODUCTE. Per editar o eliminar 
un producte clica sobre el seu nom i t'apareixerà la seva fitxa. Pots buscar productes 
per categoria i/o per proveïdora. Per defecte apareixen tots els productes ordenats per ordre
alfabètic.</p>
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