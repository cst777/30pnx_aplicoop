<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) 
{
	$user = $_SESSION['user'];
	
	$pcat=$_POST['cat'];
	$psubcat=$_POST['subcat'];
	$pprov=$_POST['prov'];
	$pdatas=$_POST['datas'];
	$pdatai=$_POST['datai'];

	include 'config/configuracio.php';
	?>

	<html>
	<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
	<link rel="stylesheet" type="text/css" href="coope.css" />
	<title>estadística de consum de productes ::: la coope</title>
					 <!-- calendar stylesheet -->
  <link rel="stylesheet" type="text/css" media="all" href="calendar/calendar-win2k-1.css" title="win2k-1" />

  <!-- main calendar program -->
  <script type="text/javascript" src="calendar/calendar.js"></script>

  <!-- language for the calendar -->
  <script type="text/javascript" src="calendar/lang/calendar-cat.js"></script>

  <!-- the following script defines the Calendar.setup helper function, which makes
       adding a calendar a matter of 1 or 2 lines of code. -->
  <script type="text/javascript" src="calendar/calendar-setup.js"></script>

	</style>
	</head>
	
	<body>
	<div class="pagina" style="margin-top: 10px;">
	<div class="contenidor_1" style="border: 1px solid black;">
	<p class='path'> 
	><a href='admint.php'>administració</a> 
	>><a href='estat_consum.php'>estadística consum</a> 
	</p>
	
	<p class="h1" style="background: black; text-align: left; padding-left: 20px;">
	Estadística consum</p>
	
	<table width="95%" align="center">
	<form action="estat_consum.php" method="post" name="prod" id="prod">
	<tr class="cos_majus" style="padding-top: 10px;">
	<td width="20%" align="center" class="form">Categories</td>
	<td width="20%" align="center" class="form">Subcategories</td>
	<td width="20%" align="center" class="form">Proveidores</td>
	<td width="20%" align="center" class="form">Superior a la data</td>
	<td width="20%" align="center" class="form">Inferior a la data</td>
	</tr>

	<tr class="cos">
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
		$opt_sc='<OPTION value="">subcategories</option>';
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
	<td align="center">
<input type="text" value="<?php echo $pdatas; ?>" name="datas" id="f_date_a" size="8" maxlength="10" readonly />
<button type="text" name="budi" id="f_trigger_a">...</button>
<button type="submit" name="okds" id="okds">ok</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_a",     // id of the input field
        ifFormat       :    "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
        button         :    "f_trigger_a",  // trigger for the calendar (button ID)
        singleClick    :    true
    });
</script>
</td>

<td align="center">
<input type="text" value="<?php echo $pdatai; ?>" name="datai" id="f_date_b" size="8" maxlength="10" readonly />
<button type="text" name="budf" id="f_trigger_b">...</button>
<button type="submit" name="okdi" id="okdi">ok</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_b",     // id of the input field
        ifFormat       :    "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
        button         :    "f_trigger_b",  // trigger for the calendar (button ID)
        singleClick    :    true
    });
</script>
</td>
	</form>
	</tr></table>

<div class="contenidor_fac" style="border: 0px solid black; width: 900px;">

	<?php
	if ($pcat!="" OR $pprov!="" OR $pdatas!="" OR $pdatai!="")
	{
		$datas2 = explode ("/",$pdatas);
		$datai2 = explode ("/",$pdatai);
		$datasup = $datas2[2]."-".$datas2[1]."-".$datas2[0];
		$datainf = $datai2[2]."-".$datai2[1]."-".$datai2[0];
		if ($pcat!=""){$wpcat="AND pr.categoria='".$pcat."'"; $tpcat='la categoria '.$pcat;}
		else {$wpcat=""; $tpcat="";}
		if ($psubcat!=""){$wpsubcat="AND pr.subcategoria='".$psubcat."'"; $tpsubcat='la subcategoria '.$psubcat;}
		else {$wpsubcat=""; $tpsubcat="";}
		if ($pprov!=""){$wpprov="AND pr.proveidora='".$pprov."'"; $tpprov='la proveïdora '.$pprov;}
		else {$wpprov=""; $tpprov="";}
		if ($pdatas!=""){$wpdatas="AND c.data>='".$datasup."'"; $tpdatas='data superior a '.$pdatas;}
		else {$wpdatas=""; $tpdatas="";}
		if ($pdatai!=""){$wpdatai="AND c.data<='".$datainf."'"; $tpdatai='data inferior a '.$pdatas;}
		else {$wpdatai=""; $tpdatai="";}
		$where=$wpcat." ".$wpsubcat." ".$wpprov." ".$wpdatas." ".$wpdatai;
		$title='Recerca per '.$tpcat.' '.$tpsubcat.' '.$tpprov.' '.$tpdatas.' '.$tpdatai;	
	}
	else
	{
		$where=""; $title="Ordenació alfabètica de productes";
	}
		
		print ('<p class="h1" 
		style="background: black; font-size:14px; text-align: left; 
		height: 20px; padding-left: 20px;">'.$title.'</p>');
	 
		print('<table width="100%" align="center" cellspading="5" cellspacing="5" >
		<tr class="cos_majus">
			<td width="30%" align="center">Producte</td><td width="10%" align="center">Proveidora</td>
			<td width="10%" align="center">Categoria</td><td width="10%" align="center">Subcategoria</td>
			<td width="10%" align="center">Consum</td><td width="10%" align="center">Despesa</td>
			<td width="10%" align="center">Data inf</td><td width="10%" align="center">Data sup</td>
			</tr>');
	
		$sel = "SELECT cl.ref, pr.nom, pr.proveidora, pr.unitat, pr.categoria, pr.subcategoria, 
				SUM(cl.cistella), SUM(cl.preu*cl.cistella), MIN(c.data), MAX(c.data)
			FROM comanda AS c, comanda_linia AS cl, productes AS pr
			WHERE c.numero=cl.numero AND pr.ref=cl.ref ".$where."
			GROUP BY cl.ref";
		$result = mysql_query($sel);
		if (!$result) {die('Invalid query: ' . mysql_error());}
	
		$k=0;
		while (list($ref,$nomprod,$nomprov,$unitat,$cat,$subcat,$consum,$despesa,$datamin,$datamax)= mysql_fetch_row($result))
		{	
			$datas3 = explode ("-",$datamax);
			$datai3 = explode ("-",$datamin);
			$datamaxvis = $datas3[2]."-".$datas3[1]."-".$datas3[0];
			$dataminvis = $datai3[2]."-".$datai3[1]."-".$datai3[0];
			$consum=number_format($consum,3,',','.');
			$despesa=number_format($despesa,2,',','.');
?>	
			<tr class='cos'>
			<td align="center"><?php echo $nomprod; ?></td>
			<td align="center"><?php echo $nomprov; ?></td>
			<td align="center"><?php echo $cat; ?></td>
			<td align="center"><?php echo $subcat; ?></td>
			<td align="center"><?php echo $consum." ".$unitat; ?></td>
			<td align='center'><?php echo $despesa; ?> €</td>
			<td align="center"><?php echo $dataminvis; ?></td>
			<td align="center"><?php echo $datamaxvis; ?></td>
			</tr>

<?php
			$k++;
		} 
		print ('</table></div></div>');

?>
</div>

<?php
include 'config/disconect.php'; 
}
else 
{
	header("Location: index.php"); 
}
?>