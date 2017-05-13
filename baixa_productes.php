<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) 
{
	$user = $_SESSION['user'];
	
	$pcat=$_POST['cat'];
	$psubcat=$_POST['subcat'];
	$pprov=$_POST['prov'];

	$gref = $_GET['id'];
	$gactiu = $_GET['id3'];
	$gpcat = $_GET['id4'];
	$gpsubcat = $_GET['id5'];
	$gpprov = $_GET['id6'];

	include 'config/configuracio.php';
	?>

	<html>
	<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
	<title>activar productes ::: la coope</title>
	</head>
	
<body>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid #990000;">
<p class='path'> 
><a href='admint.php'>administració</a>  
>><a href='baixa_productes.php'>activar i donar de baixa productes</a>
</p>

<p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">
Activar i donar de baixa productes</p>

<?php
	if ($gactiu != "")
	{
		$query3 = "UPDATE productes
			SET actiu='".$gactiu."'
			WHERE ref='".$gref."'";
		mysql_query($query3) or die('Error, insert query3 failed');
		
		$pcat=$gpcat;
		$psubcat=$gpsubcat;
		$pprov=$gpprov;		
	}
?>

<table width="80%" align="center">
<form action="baixa_productes.php" method="post" name="prod" id="prod">
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
		if ($pcat=="") {$where="WHERE proveidora='".$pprov."'"; $title="Recerca per proveïdora ".$pprov;}
		else 
		{
			if ($psubcat=="" AND $pprov=="") {$where="WHERE categoria='".$pcat."'"; $title="Recerca per categoria ".$pcat;}
			elseif ($psubcat!="" AND $pprov=="") {$where="WHERE categoria='".$pcat."' AND subcategoria='".$psubcat."'"; $title="Recerca per categoria ".$pcat." i subcategoria ".$psubcat;}
			elseif ($psubcat=="" AND $pprov!="") {$where="WHERE categoria='".$pcat."' AND proveidora='".$pprov."'"; $title="Recerca per categoria ".$pcat." i proveïdora ".$pprov;}
			elseif ($psubcat!="" AND $pprov!="") {$where="WHERE categoria='".$pcat."' AND subcategoria='".$psubcat."' AND proveidora='".$pprov."'"; $title="Recerca per categoria ".$pcat.", subcategoria ".$psubcat." i proveïdora ".$pprov;}
		}
	}
	else
	{
	$where=""; $title="Ordenació alfabètica de productes";
	}
		
		print ('<p class="h1" 
		style="background: #990000; font-size:14px; text-align: left; 
		height: 20px; padding-left: 20px;">'.$title.'</p>');
	 
	 	print('<table width="95%" align="center" style="padding:0px;" >');
	 	
		print('<tr class="cos_majus">
			<td width="20%" align="center">Producte</td><td width="12%" align="center">Actiu</td>
			<td width="20%" align="center">Producte</td><td width="12%" align="center">Actiu</td>
			<td width="20%" align="center">Producte</td><td width="12%" align="center">Actiu</td>
			</tr>');
	
		$sel = "SELECT ref,nom,proveidora,actiu FROM productes ".$where." ORDER BY nom";
		$result = mysql_query($sel);
		if (!$result) {die('Invalid query: ' . mysql_error());}
	
		$i=0;
		$k=0;
		while (list($ref,$nomprod,$nomprov,$actiu)= mysql_fetch_row($result))
		{	
			$checked1="";
			$checked2="";
			if ($actiu=="actiu") {$checked1="checked";}
			else {$checked2="checked";}
			if ($i==0) {print ('<tr>');}
?>	

			<td class="cos"><?php echo $nomprod; ?></td>
			<td class="cos">
			si<input type="radio" name="actiu<?php echo $k; ?>" value="actiu" id="actiu<?php echo $k; ?>" <?php echo $checked1; ?> onClick="javascript:window.location = 'baixa_productes.php?id=<?php echo $ref; ?>&id3=actiu&id4=<?php echo $pcat; ?>&id5=<?php echo $psubcat; ?>&id6=<?php echo $pprov; ?>';">
			no<input type="radio" name="actiu<?php echo $k; ?>" value="baixa" id="actiu<?php echo $k; ?>" <?php echo $checked2; ?> onClick="javascript:window.location = 'baixa_productes.php?id=<?php echo $ref; ?>&id3=baixa&id4=<?php echo $pcat; ?>&id5=<?php echo $psubcat; ?>&id6=<?php echo $pprov; ?>';">
			</td>

<?php

			$i++;
			$k++;
			if ($i==3) {print ('</tr>'); $i=0;}
		} 
		print ('</table></div></div>');

?>
<p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
	Per activar o desactivar productes clica al botó corresponent i s'aplicarà automàticament. 
	Pots buscar productes per categoria i/o per proveïdora. Per defecte apareixen tots els 
	productes ordenats per ordre alfabètic.</p>

</div></body></html>

<?php
include 'config/disconect.php'; 
}
else 
{
	header("Location: index.php"); 
}
?>