<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

	$sessionid=$_SESSION['sessionid'];
	
	$gfam=$_GET['id'];
	$gdata=$_GET['id2'];
	$gproces=$_GET['id3'];
	$ggrup=$_GET['id4'];
	$gvis=$_GET['id5'];
	$gnumfact=$_GET['id6'];
	
	//vol evitar que algú es col·li escrivint codi directament a l'adreça html //
	if ($_SESSION['codi_cistella'] != 'in')
	{
		$gvis=0;
	}
	////
	
	list($mdiatdx, $mestdx, $anytdx ) = explode("-", $gdata);
	$gbd_data=$anytdx."-".$mestdx."-".$mdiatdx;
	
/// Hi ha dos possbile retorns POST ///
/// Un de cistella2_fam.php///
	$post_cistella=$_POST["num"];
	$post_ref=$_POST["ref"];

/// Un altre de cistella_mes.php ///	
	$paddfam=$_POST['nouf'];
	$pprov=$_POST['prov'];
	$pprod=$_POST['prod'];
	$pref=$_POST['ref'];
	$pnum=$_POST['num'];
	
	if ($gvis==0)
	{
		$readonly="readonly";
		$button="";
		$sty="padding:4px 0px; height: 20px;";
		$intronovafam="";
	}
	else 
	{
		$readonly="";
		$button='<input class="button2" style="width: 120px;" name="Gcodi" type="button" value="PAS SEGÜENT" onClick="confirma()">';
		$sty="";
		$intronovafam='<input class="button2" style="width:160px;" type="button" value="INTRODUIR NOVA FAMÍLIA" 
		onClick="javascript:window.location = \'cistella_mes.php?id3='.$gdata.'&id5='.$gvis.'&id6='.$gproces.'&id7='.$ggrup.'&id8=1\'">';
	}


	include 'config/configuracio.php';
	
	

	///////////
	
	//////////////////////////////////////////////////
	//// Si existeix la variable POST procedent de cistella_mes.php ///
	/// vol dir que volem afegir una família per aquest producte en aquest procés///
	/// llavors: ///
	/// Si ja existeix comanda de la família en el proces ///
	/// Inserta una nova linia a comanda linia ///
	/// Si no existeix comanda ///
	/// Crea comanda de la família per aquest procés i la línia corresponent al producte ///
	///////////////////////////////////////////////// 
	if ($paddfam)
	{		
		if($pnum!="") 
		{		
			$query2 = "INSERT INTO comanda_linia (numero, ref, quantitat, cistella)
				VALUES ('$pnum', '$pref', '1', '0')";
			mysql_query($query2) or die('Error, insert query2 failed');
		}
		else 
		{
			$query3 = "INSERT INTO comanda ( `usuari` , `proces`, `grup`, `sessionid` , `data` )
				VALUES ('$paddfam', '$gproces', '$ggrup', '$sessionid', '$gbd_data')";
			mysql_query($query3) or die('Error, insert query3 failed');
			$inumcmda=mysql_insert_id(); 		

			$query4 = "INSERT INTO comanda_linia (numero, ref, quantitat, cistella)
				VALUES ('$inumcmda', '$pref', '1', '0')";
			mysql_query($query4) or die('Error, insert query4 failed'); 	
		}	
	}	

	////////////////////////////////////////////
	// si hi ha dades d'un producte GET id i POST llavors les guarda //
	// implica retorn de cistella2_fam.php /////////
	// amb informació de les cistelles per guardar //
	/////////////////////////////////////////////////
	
	if ($gfam!="") 
	{
		$files = count($post_cistella);
		
		////Busquem el numero de comanda////
		$query= "SELECT c.numero FROM comanda AS c
		WHERE c.data='$gbd_data' AND c.proces='$gproces' AND c.grup='$ggrup' AND c.usuari='$gfam'";
		$result=mysql_query($query);
		if (!$result) { die("Query to show fields from table failed");}
		list($pnumcmda)=mysql_fetch_row($result);
	
		$count=0;
		for ($i=0; $i<$files; $i++) 
		{
			
			if ($post_cistella[$i]==""){$post_cistella[$i]=0;}
			
			///Busquem el nomprod i nomprov de $post_ref///
			$query0= "SELECT nom, proveidora FROM productes WHERE ref='$post_ref[$i]'";
			$result0=mysql_query($query0);
			if (!$result0) { die("Query0 to show fields from table failed");}
			list($post_prod,$post_prov)=mysql_fetch_row($result0);			
			
			///Busquem si existeixen quantitats de cistella introduides anteriorment///
			$select= "SELECT cistella FROM comanda_linia	
			WHERE numero='$pnumcmda' AND ref='$post_ref[$i]'";
			$result = mysql_query($select);
			if (!$result) { die('Invalid query select: ' . mysql_error());}
			list($c)= mysql_fetch_row($result);
			
			///Busquem si és un producte d'estoc///
			$select9= "SELECT pr.categoria, cat.estoc
			FROM productes AS pr, categoria AS cat
			WHERE pr.categoria=cat.tipus AND pr.ref='$post_ref[$i]'";
			$result9 = mysql_query($select9);
			if (!$result9) { die('Invalid query select9: ' . mysql_error());}
			list($scat,$sestoc)= mysql_fetch_row($result9);
			
			///Si és d'estoc i te introduida una quantitat a cistella ///
			/// llavors la recupera del camp estoc de la taula productes ///
			if ($c!="" AND $sestoc=='si')
			{
				$query6= "UPDATE productes 
				SET estoc=estoc+'$c'
				WHERE ref='$post_ref[$i]'";
				mysql_query($query6) or die('Error, insert query6 failed');
			}
			
			
			/// Calculem el pvp sense iva. Preu*marge ///
			$select10= "SELECT preusi, iva, marge, descompte FROM productes WHERE ref='$post_ref[$i]'";
			$result10 = mysql_query($select10);
			if (!$result10) { die('Invalid query select10: ' . mysql_error());}
			list($spreusi,$siva,$smarge,$sdescompte)= mysql_fetch_row($result10);
										
			$pvp=$spreusi*(1+$smarge);
			$pvp=sprintf("%01.2f", $pvp);	
			$pvp2=$pvp*(1-$sdescompte);
			$pvp2=sprintf("%01.2f", $pvp2);	
		
			///Introduim les noves quantitats de cistella///
			$query= "UPDATE comanda_linia
			SET cistella='$post_cistella[$i]', preu='$pvp', iva='$siva', descompte='$sdescompte'
			WHERE numero='$pnumcmda' AND ref='$post_ref[$i]'";
			mysql_query($query) or die('Error, insert query failed');
			
			if ($sestoc=='si')
			{
				$query7= "UPDATE productes 
				SET estoc=estoc-'$post_cistella[$i]'
				WHERE ref='$post_ref[$i]'";
				mysql_query($query7) or die('Error, insert query7 failed');
			}	
		}

		$nota="<p class='error' style='font-size: 14px;'>S'han introduït correctament les dades de la cistella de la família ".$gfam." corresponents a la comanda numero ".$pnumcmda."</p>";
	}

///Inici html///
?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />			
		<title>fer la cistella - llista famílies ::: la coope</title>
</head>
<style type="text/css">
			a#color:link, a#color:visited {color:white; border: 1px solid #9cff00;}
			a#color:hover {color:black; border: 1px solid #9cff00;   -moz-border-radius: 10%;}
   		a#color:active {color:white; border: 1px solid #9cff00;  -moz-border-radius: 10%;}
   		a#color2:link, a#color2:visited, a#color2:hover, a#color2:active {color:black;}
			</style>
<script language="javascript" type="text/javascript">
			function confirma()
			{
				var answer;
				var answer = confirm("Estas tancant l'apartat de FER CISTELLES \nS'enviarà notificació electrònica a totes les famílies i es generarà un codi d'edició. \nAcceptar: Anar a facturació \nCancelar: Continuar fent cistelles");
				if (answer)
				{
	  				window.location = 'cistella_check1.php?id=<?php echo $gproces."&id2=".$ggrup."&id3=".$gbd_data; ?>';
				}
			}
</script>

<body>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid green;">
<p class='path'> 
><a href='admint.php'>administració</a> 
>><a href='grups_comandes.php'>grups de comandes i cistelles</a>
>>><a href='cistelles2.php?id2=<?php echo $gdata."&id3=".$gproces."&id4=".$ggrup."&id5=".$gvis; ?>'>cistella <?php echo $gdata." - ".$gproces." - ".$ggrup; ?></a>  
</p>
<p class="h1" style="background: green; text-align: left; padding-left: 20px;">
cistella <?php echo $gdata." - ".$gproces." - ".$ggrup; ?>
<span style="display: inline; float: right; text-align: center; vertical-align: middle; padding: 2px 50px 2px 0px;">
<input class="button2" style="width:160px; margin-left:10px;" type="button" value="CISTELLES PER PRODUCTE" 
onClick="javascript:window.location = 'cistelles.php?id2=<?php echo $gdata.'&id3='.$gproces.'&id4='.$ggrup.'&id5='.$gvis; ?>'">
</span></p>
<?php echo $nota; ?>

<div class="contenidor_fac" style="border: 1px solid green; max-height: 350px; overflow: scroll; overflow-x:hidden;">
<p class="h1" style="background: green; text-align: left; padding-left: 20px;">
Llista famílies
<span style="display: inline; float: right; text-align: center; vertical-align: middle; padding: 2px 50px 2px 0px;">
<?php echo $intronovafam; ?>
</span>
</p>



<table width="60%" align="center" valign="middle" cellpadding="5" cellspacing="5">

<tr class='cos_majus'><td align="center">Família</td>
<td align="center">Numero comanda</td>
<td align="center">Unitats demanades</td>
<td align="center">Unitats servides</td>
</tr> 


<?php

	$taula3 = "SELECT c.numero, c.usuari, SUM(cl.quantitat), SUM(cl.cistella) 
	FROM comanda_linia AS cl, comanda AS c
	WHERE cl.numero=c.numero AND c.data='$gbd_data'	AND c.proces='$gproces' AND c.grup='$ggrup'
	GROUP BY c.numero	ORDER BY c.usuari";
	
	$result3 = mysql_query($taula3);
	if (!$result3) {die('Invalid query3: ' . mysql_error());}

	$i=0;
	while(list($numero,$familia,$demanat,$servit)=mysql_fetch_row($result3))
	{
		$color="";
		if ($servit!=0) 
			{
			$color="style='color: green;'";
			}
		$estil="";
		if ($servit!=0 AND $servit<>$demanat) 
			{
			$color="style='color: red;'";
  			}
?>


<tr class="cos">
<td align="center">
<a id='color2' href="cistella2_fam.php?id=<?php echo $familia.'&id2='.$gdata.'&id3='.$gproces.'&id4='.$ggrup.'&id5='.$gvis; ?>" >
<?php echo $familia; ?></a></td>
<td align="center"><?php echo $numero; ?></td>
<td align="center" <?php echo $color; ?>><?php echo $demanat; ?></td>
<td align="center" <?php echo $color; ?>><?php echo $servit; ?></td>
</tr>

<?php
		$i++;
	}
?>
</table>
</div>


<p class="linia_button2" style="<?php echo $sty; ?> background: green; text-align: center; vertical-align: middle;">

<?php 
	if ($gvis=='1')
	{
		echo $button; 
	}
?>

<input class="button2" type="button" value="Sortir" onClick="location.href='grups_comandes.php'">
</p>

</form>

</div></div>
</body>
</html>


<?php
include 'config/disconect.php';
} 
else {
header("Location: index.php"); 
}
?>