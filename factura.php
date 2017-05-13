<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' OR ($_GET['id'] != "" AND strlen($_GET['id2']) == 12 AND ($_GET['id3'] == "0" OR $_GET['id3'] == "1"))) 
{
	$user = $_SESSION['user'];
	$superuser=strtoupper($_SESSION['user']);
	$numcmda=$_GET['id'];
	$codi=$_GET['id2'];
	$val=$_GET['id3'];

	include 'config/configuracio.php';
	
	date_default_timezone_set('Europe/Madrid');
   $session= date ("Y-m-d H:i:s", $_SESSION['timeinitse']);
	
?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>factura ::: la coope</title>
	</head>
	
	<style type="text/css" media="print">
    .NonPrintable
    {
      display: none;
    }
  </style>

<body> 
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid green;">
<div class="contenidor_fac">
	
	<div class="NonPrintable">

<?php
	if ($_SESSION['image_is_logged_in'] != 'true')
	{
		$sel2="SELECT numero, report0
		FROM comanda
		WHERE numero='$numcmda' AND report0='$codi'";
		$query2=mysql_query($sel2) or die('query2:'.mysql_error());
		if (mysql_num_rows($query2)!=1) 
		{
			die ('<p class="error" style="font-size: 14px;">
			Entra a l\'aplicatiu per veure la factura '.$numcmda.' o valida-la des del correu 
			de l\'escriptori</p>');
		}
	}	

	if ($val=='1')
	{
		$sel3="UPDATE comanda SET check1='1' WHERE numero='$numcmda'";
		$query3=mysql_query($sel3) or die('query3:'.mysql_error());
		
		$sel2a="SELECT numfact, data2 FROM comanda WHERE numero='$numcmda'";
		$query2a=mysql_query($sel2a) or die('query2a:'.mysql_error());
		list($numfact2, $data2)=mysql_fetch_row($sel2a);
		
		$yearfact=strtotime($data2);
		$yearfact=date('Y',$yearfact);
		$numfact2=$numfact2."/".$yearfact;
				
		echo '<p class="error" style="font-size: 14px;">La factura '.$numfact2.' ha estat validada 
		satisfactòriament</p>';
	}
	else
	{
	
		$sel="SELECT c.usuari, u.nomf, u.adressf, u.niff, c.proces, c.data, c.check0, c.report0, c.data2, c.check1, c.numfact, c.notes
		FROM comanda AS c, usuaris AS u
		WHERE c.numero='$numcmda' AND c.usuari=u.nom";
		$query=mysql_query($sel) or die('query:'.mysql_error());
		list($familia, $nomf, $adressf, $niff, $proces, $bd_data, $check0, $acatreport0, 
		$bd_data2, $check1, $numfact, $notes) = mysql_fetch_row($query);
		
		/// creem numero de factura
		$factyear=strtotime($bd_data2);
		$factyear=date('Y',$factyear);
		$numfact=$numfact."/".$factyear;
	
		list($anyrc, $mesrc, $mdiarc) = explode("-", $bd_data);
		$ver_data=$mdiarc." - ".$mesrc." - ".$anyrc;
		list($anyrc2, $mesrc2, $mdiarc2) = explode("-", $bd_data2);
		$ver_data2=$mdiarc2." - ".$mesrc2." - ".$anyrc2;
		$yearfact=$anyrc2;
		
		if ($val=='2')
		{			
			echo '<p class="error" style="font-size: 14px;">La factura '.$numfact.' 
			ha estat validada satisfactòriament</p>';
		}

?>

	<p class="linia_button2" style="background: green; text-align: center; vertical-align: middle;">
	<input class="button2" type="button" name="imprimir" value="IMPRIMIR" onclick="window.print();">

<?php
		if ($_SESSION['image_is_logged_in'] == 'true')
		{
			echo'<input class="button2" type="button" value="ENRERE" onClick="javascript: history.go(-1);">';
		}
		//$logo_factura està definida a l'arxiu de configuració
?>	
	
	</p>
	</div>
	
			<div class="contenidor_4" style="float:left;">
				<img id="fig" style="width:175px; height:85px; padding: 10px 0px 20px 0px ;" 
				src="<?php echo $logo_factura; ?>"> 
			</div>

			<div style="width: 510px; float:right;">
				<p class="cos16" 
				style="font-weight: bold; text-align: left; padding-left:40px; padding-right:25px; 
					 vertical-align: middle;">
				<span style="color: grey;">Factura nº </span><?php echo $numfact; ?>
				<br/>
				<span style="color: grey;">Data: </span><?php echo $ver_data2; ?>
				<br/>
				<span style="color: grey;">Nom família: </span><?php echo $nomf; ?>
				<br/>
				<span style="color: grey;">Adreça: </span><?php echo $adressf; ?>
				<br/>
				<span style="color: grey;">NIF: </span><?php echo $niff; ?>
				</p>
			</div>
			
			<div style="clear: both; border: 1px solid green;">
		<table width="100%" align="center" style="padding:15px;">
			<tr class="cos_majus" valign="baseline">
				<td width="50%" align="left" style="padding:15px 0px;"><u>Producte</u></td>
				<td width="15%" align="center"><u>Quant.</u></td>
				<td width="10%" align="center"><u>Preu</u></td>
				<td width="10%" align="center"><u>Desc</u></td>
				<td width="10%" align="center"><u>Iva</u></td>
				<td width="10%" align="right"><u>Total</u></td>																
			</tr>

<?php
		$sel5="SELECT cl.ref, prod.nom, prod.proveidora, prod.unitat, cl.cistella, cl.preu, cl.descompte, cl.iva
		FROM comanda_linia AS cl, productes AS prod
		WHERE cl.numero='$numcmda' AND cl.ref=prod.ref 
		ORDER BY prod.categoria, prod.proveidora, prod.nom";
		$result5=mysql_query($sel5) or die(mysql_error());

		$total=0; $total_import_brut=0; $totaliva=0;
		while (list ($ref, $nomprod, $nomprod2, $unitat, $cistella, $preu, $descompte, $iva)= 
		mysql_fetch_row($result5)) 
		{
			/// agafem la primera lletra de la unitat ///
/* Modified by 30Panxes Dec. 2016 
			$unitat1=substr($unitat,0,1); 			
*/
			$unitat1=$unitat; 

			//calculem import brut, iva línia, subtotal linia, 
			///i totals import brut, iva i factura
			$importbrut=$cistella*$preu*(1-$descompte);
			$total_import_brut=$total_import_brut+$importbrut;
			$subtotal=$cistella*$preu*(1-$descompte)*(1+$iva);
			$subtotal=sprintf("%01.2f", $subtotal);
			$total=$total+$subtotal;
			$iva_linia=$cistella*$preu*(1-$descompte)*$iva;
			$iva_linia=sprintf("%01.2f", $iva_linia);
			$totaliva=$totaliva+$iva_linia;
			
			//iva i descompte si =0 apreixen en blanc//
			$v_descompte=$descompte*100;
			$v_iva=$iva*100;			
			if ($iva==0){$v_iva="";} else {$v_iva=$v_iva." %";}
			if ($descompte==0){$v_descompte="";} else {$v_descompte=$v_descompte." %";}
			

?>

				<tr class="cos">
				<td><?php echo $ref.' - '.$nomprod; ?></td>
				<td align="center"><?php echo $cistella.' - '.$unitat1; ?>.</td>
				<td align="center"><?php echo $preu; ?>&#8364;</td>
				<td align="center"><?php echo $v_descompte; ?></td>
				<td align="center"><?php echo $v_iva; ?></td>
				<td align="right"><?php echo $subtotal; ?>&#8364;</td>
				</tr>

<?php
		}
		$total=sprintf("%01.2f", $total);
		$totaliva=sprintf("%01.2f", $totaliva);
		$total_import_brut=sprintf("%01.2f", $total_import_brut);
		
		if ($val=='2')
		{
			$sel3="UPDATE comanda SET check2='1' WHERE numero='$numcmda'";
			$query3=mysql_query($sel3) or die('query3: '.mysql_error());
			
			$sel4="INSERT INTO moneder VALUES ('".$session."','".$user."','".date('Y-m-d')."',
			'".$familia."','Factura num. ".$numcmda."','-".$total."','')";
			$query4=mysql_query($sel4) or die('query4: '.mysql_error());
			
			$sel5="UPDATE usuaris SET moneder=moneder-".$total." WHERE nom='".$familia."'";
			$query5=mysql_query($sel5) or die('query5: '.mysql_error());
			
		}
?>
		</table>
		<table width="100%" align="center">	
		<tr class="cos_majus" valign="baseline">
				<td width="33%" align="center" style="padding:15px 0px;"><u>Imp. Brut</u></td>
				<td width="33%" align="center" style="padding:15px 0px;"><u>Iva</u></td>
				<td width="33%" align="center"><u>TOTAL</u></td>
		</tr>
		<tr class="cos12" style="font-weight: bold;">
				<td align="center"><?php echo $total_import_brut; ?>&#8364;</td>
				<td align="center"><?php echo $totaliva; ?>&#8364;</td>
				<td align="center"><?php echo $total	; ?>&#8364;</td>
		</tr>
	</table>
	</div>
	<p class="cos2" style="clear: both; text-align: left;">
	Les teves dades procedeixen d’un fitxer del que és propietari i responsable aquesta entitat, 
	davant la qual poden exercitar els drets d’accés, rectificació, cancel•lació i oposició 
	reconeguts per la LO 15/1999, de 13 de desembre, de protecció de dades de caràcter personal.
	</p>

<?php
	}
	echo"</div></div></div></body></html>";
	include 'config/disconect.php';
} 
else 
{
	header("Location: index.php"); 
}
?>


