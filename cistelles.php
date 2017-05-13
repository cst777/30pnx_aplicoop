<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) 
{
	$user = $_SESSION['user'];
	$sessionid=$_SESSION['sessionid'];

	$gprodref=$_GET['id'];
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
/// Un de cistella_prod.php///
	$post_cistella=$_POST["num"];
	$post_familia=$_POST["nom"];
	$post_numcmda=$_POST["numcmda"];
/// Un altre de cistella_mes.php ///	
	$paddfam=$_POST['nouf'];
	$pprov=$_POST['prov'];
	$pprod=$_POST['prod'];
	$pref=$_POST['ref'];
	$pnum=$_POST['num'];
	
	// si hi ha un numero comanda de factura //
	if ($gnumfact!=""){$aw='AND c.numero='.$gnumfact; $id6='&id6='.$gnumfact; $id8='&id8='.$gnumfact;}
	else {$aw=""; $id6=""; $id8="";}
//

	include 'config/configuracio.php';
	$nota="";
	
	
	////////////////////////////////////////////
	/// Si hi ha un POST procedent de cistella_mes.php ///
	/// llavors s'ha d'afegir el producte al proces ///
	/// creant si no existeix una comanda per a una família ///
	//////////////////////////////////////////////
	
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
	// implica retorn de cistelles_prod.php /////////
	// amb informació de les cistelles per guardar //
	/////////////////////////////////////////////////
	
	if ($gprodref!="") 
	{
		$files = count($post_cistella);
	
	/// Busquem nomprod i nomprov a partir de prodref ////
	$query0= "SELECT nom, proveidora FROM productes WHERE ref='$gprodref'";
	$result0=mysql_query($query0);
	if (!$result0) { die("Query0 to show fields from table failed");}

	list($gnomprod,$gprov)=mysql_fetch_row($result0);
	///////////
				
		$select9= "SELECT pr.categoria, cat.estoc
		FROM productes AS pr, categoria AS cat
		WHERE pr.categoria=cat.tipus AND pr.ref='$gprodref'";
		$result9 = mysql_query($select9);
		if (!$result9) { die('Invalid query select9: ' . mysql_error());}

		list($scat,$sestoc)= mysql_fetch_row($result9);
		
		$count=0;
		for ($i=0; $i<$files; $i++) 
		{
			
			if ($post_cistella[$i]==""){$post_cistella[$i]=0;}

			$select= "SELECT cistella 
			FROM comanda_linia 
			WHERE numero='$post_numcmda[$i]' AND ref='$gprodref'";
			$result = mysql_query($select);
			if (!$result) { die('Invalid query select: ' . mysql_error());}

			list($c)= mysql_fetch_row($result);
			
			// Si estem editant la variable cistella, primer introdueix el seu valor a l estoc ///
						
				if ($c!="" AND $sestoc=='si')
				{
				$query6= "UPDATE productes 
				SET estoc=estoc+'$c'
				WHERE ref='$gprodref'";
				mysql_query($query6) or die('Error, insert query6 failed');
				}
			
			///////////////////////////////////////
			/// Calculem el pvp sense iva. Preu*marge ///
			///////////////////////////////////////			
			
				$select10= "SELECT preusi, iva, marge, descompte FROM productes WHERE ref='$gprodref'";
				$result10 = mysql_query($select10);
				if (!$result10) { die('Invalid query select10: ' . mysql_error());}
				list($spreusi,$siva,$smarge,$sdescompte)= mysql_fetch_row($result10);
											
				$pvp=$spreusi*(1+$smarge);
				$pvp=sprintf("%01.2f", $pvp);		
				$pvp2=$pvp*(1-$sdescompte);
				$pvp2=sprintf("%01.2f", $pvp2);
						
				$query= "UPDATE comanda_linia
					SET cistella='$post_cistella[$i]', preu='$pvp', iva='$siva', descompte='$sdescompte'
				WHERE numero='$post_numcmda[$i]' AND ref='$gprodref'";
				mysql_query($query) or die('Error, insert query failed');
			
				if ($sestoc=='si')
				{
					$query7= "UPDATE productes 
					SET estoc=estoc-'$post_cistella[$i]'
					WHERE ref='$gprodref'";
					mysql_query($query7) or die('Error, insert query7 failed');
			}	
		}

		$nota="<p class='error' style='font-size: 14px;'>S'han introduït correctament les dades de la cistella del producte ".$gnomprod."-".$gprov."</p>";
	}
	
	?>

	<html>
		<head>
			<meta http-equiv="content-type" content="text; charset=UTF-8" >
			<link rel="stylesheet" type="text/css" href="coope.css" />			
			<title>fer la cistella ::: la coope</title>
			
			<style type="text/css">
			a#color:link, a#color:visited {color:white; border: 1px solid #9cff00;}
			a#color:hover {color:black; border: 1px solid #9cff00;   -moz-border-radius: 10%;}
   		a#color:active {color:white; border: 1px solid #9cff00;  -moz-border-radius: 10%;}
   		a#color2:link, a#color2:visited, a#color2:hover, a#color2:active {color:black;}
			</style>
		</head>

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

<?php

	$taula3 = "SELECT check1
			FROM cistella_check
			WHERE proces='$gproces' AND grup='$ggrup' AND data='$gbd_data'";			
	$result3 = mysql_query($taula3);
	if (!$result3) {die('Invalid query3: ' . mysql_error());}
	list($check)=mysql_fetch_row($result3);
	
	/// Si no es pot editar (gvis=0) no hi ha botó de "pas segúent" ni d'"introduir nou producte" ///
	if ($gvis==0) 
	{
		$link_cap=">>><a class='Estilo2' href='cistelles.php?id2=".$gdata."&id3=".$gproces."&id4=".$ggrup."&id5=0'>veure la cistella ".$gdata." - ".$gproces." - ".$ggrup."</a>";
		$title="Veure la cistella ".$gdata." - ".$gproces." - ".$ggrup;
		$button='<input class="button2" type="button" value="CREAR ARXIU CSV" style="width: 120px;"
		onClick="javascript:window.location = \'createcsv.php?id='.$gbd_data.'&id2='.$gproces.'&id3='.$ggrup.'&id4=2\'">';
		$sty="padding:4px 0px; height: 20px;";
		$nouproducte="";
	}
	else
	{
		$button='<input class="button2" style="width: 120px;" name="Gcodi" type="button" value="PAS SEGÜENT" onClick="confirma()">';
		$sty="";
		$nouproducte='<input class="button2" style="width:150px;" type="button" value="AFEGIR NOU PRODUCTE" 
		onClick="javascript:window.location = \'cistella_mes.php?id3='.$gdata.'&id5='.$gvis.'&id6='.$gproces.'&id7='.$ggrup.'\'">';
		if ($check==0)
		{
			$link_cap=">>><a class='Estilo2' href='cistelles.php?id2=".$gdata."&id3=".$gproces."&id4=".$ggrup."&id5=1'>fer la cistella ".$gdata." - ".$gproces." - ".$ggrup."</a>";
			$title="Fer la cistella ".$gdata." - ".$gproces." - ".$ggrup;
		}
		else
		{
			$link_cap=">>><a class='Estilo2' href='cistelles.php?id2=".$gdata."&id3=".$gproces."&id4=".$ggrup."&id5=1".$id6."'>editar la cistella ".$gdata." - ".$gproces." - ".$ggrup."</a>";
			$title="Editar la cistella ".$gdata." - ".$gproces." - ".$ggrup;
		}	
	}
?>
<body>

<div class="pagina" style="margin-top: 10px;">

<div class="contenidor_1" style="border: 1px solid green;">
<p class='path'> 
><a href='admint.php'>administració</a> 
>><a href='grups_comandes.php'>grups de comandes i cistelles</a>
 <?php echo $link_cap; ?>
</p>
<p class="h1" style="background: green; text-align: left; padding-left: 20px;"><?php echo $title; ?>
<span style="display: inline; float: right; text-align: center; vertical-align: middle; 
padding: 2px 50px 2px 0px;">
<input class="button2" style="width:150px; margin-left:10px;" type="button" value="CISTELLES PER FAMILIA" 
onClick="javascript:window.location = 'cistelles2.php?id2=<?php echo $gdata.'&id3='.$gproces.'&id4='.$ggrup.'&id5='.$gvis; ?>'">
</span>
</p>
<p class="h1" style="background: green; text-align: left; padding-left: 20px;">Llista de productes per categoria
<span style="display: inline; float: right; text-align: center; vertical-align: middle; 
padding: 2px 50px 2px 0px;">
<?php echo $nouproducte; ?>
</span>
</p>

<?php echo $nota; ?>

<div class="cat" style="width: 750px; margin-left: auto; margin-right: auto;">

<?php
	$color2=array("#C0C000","#00b2ff","orange","#b20000","#14e500","red","#8524ba");
	$cc=0;
	$sel = "SELECT tipus FROM categoria ORDER BY tipus";
	$result = mysql_query($sel);
	if (!$result) {die('Invalid query: ' . mysql_error()); }
	while (list($cat)= mysql_fetch_row($result))
	{	
		$taula2 = "SELECT cl.ref, pr.nom, pr.proveidora, pr.unitat, pr.categoria, c.numero, c.data
		FROM comanda AS c, comanda_linia AS cl, productes AS pr
		WHERE c.numero=cl.numero AND cl.ref=pr.ref
		AND c.data='$gbd_data' AND pr.categoria='$cat' ".$aw;
		$result2 = mysql_query($taula2);
		if (!$result2) {die('Invalid query2: ' . mysql_error());}
	
		if (mysql_num_rows($result2)>0)
		{
			print ('<a href="#'.$cat.'" id="color" style="background: '.$color2[$cc].'; 
				margin-bottom: 5px; margin-right: 3px; white-space: -moz-pre-wrap; word-wrap: break-word;">
				<span>'.$cat.'</span></a>');
				$cc++;
				if ($cc==7){$cc=0;}
		}
	}
	echo'</div>	
	<div class="contenidor_fac" style="border: 1px solid green; max-height: 350px; overflow: scroll; overflow-x:hidden;">';
	$cc=0;
	$sel = "SELECT tipus FROM categoria ORDER BY tipus";
	$result = mysql_query($sel);
	if (!$result) {die('Invalid query: ' . mysql_error()); }
	while (list($cat)= mysql_fetch_row($result))
	{	
	
	$taula2 = "SELECT cl.ref, pr.nom, pr.proveidora, pr.unitat, pr.categoria, c.numero, c.data, SUM(cl.quantitat) AS sum, SUM(cl.cistella) AS csum
	FROM comanda AS c, comanda_linia AS cl, productes AS pr
	WHERE c.numero=cl.numero AND cl.ref=pr.ref AND c.data='$gbd_data' 
	AND c.proces='$gproces' AND c.grup='$ggrup' AND pr.categoria='$cat' ".$aw."
	GROUP BY cl.ref
	ORDER BY pr.categoria, pr.proveidora, pr.nom";

	$result2 = mysql_query($taula2);
	if (!$result2) {die('Invalid query2: ' . mysql_error());}
	
	if (mysql_num_rows($result2)>0)
	{
		print ('<a name="'.$cat.'"></a>
	  	<p class="h1"
		style="background: '.$color2[$cc].'; font-size:14px; text-align: left; 
		height: 20px; padding-left: 20px; clear: left;">'.$cat.'</p>');
		echo '<table width="80%" align="center" valign="middle" cellpadding="5" cellspacing="5">';
		echo "<tr class='cos_majus'><td width='60%'>Producte</td>";
		echo "<td width='20%' align='center'>Total comanda</td>";
		echo "<td width='20%' align='center'>Total cistella</td>";
		echo "</tr>";

		while (list($prodref,$nom_prod,$nom_prov,$uni,$t,$n,$d,$sum,$csum)=mysql_fetch_row($result2))
		{
		$suma = sprintf("%01.2f", $sum);
		$csuma= sprintf("%01.2f", $csum);
		$color="";
		if ($csuma!=0) 
			{
			$color="style='color: ".$color2[$cc].";'";
			}
		$estil="";
		if ($csuma!=0 AND $csuma<>$suma) 
			{
			$color="";
			$estil="style='color: red;'";
  			}
  		
		$link="<a id='color2' href='cistella_prod.php?id=".$prodref."&id3=".$gdata."&id4=".$cat."&id5=".$gvis."&id6=".$gproces."&id7=".$ggrup.$id8."'>".$nom_prod."-".$nom_prov."</a>";
		
?>

<tr class='cos' <?php echo $color; ?>>
<td><?php echo $link; ?></td>
<td align="center" <?php echo $estil; ?>><?php echo $suma; ?> <?php echo $uni; ?></td>
<td align="center" <?php echo $estil; ?>><?php echo $csuma; ?> <?php echo $uni; ?></td>
</tr>

<?php
		}
	echo "</table>";
	$cc++;
	if ($cc==7){$cc=0;}
	}
}
	
?>
</div>

<?php
if ($gnumfact!="")
{
echo '<p class="linia_button2" style="padding:4px 0px; height: 20px; background: green; text-align: center; vertical-align: middle;">
<input class="button2" type="button" value="SORTIR" style="width: 120px;"
 onClick="javascript:window.location = \'comandes.php?id=1 \'">
</p>';
}
else
{
echo '<p class="linia_button2" style="'.$sty.' background: green; text-align: center; vertical-align: middle;">
'.$button.' 
<input class="button2" type="button" value="SORTIR" style="width: 120px;"
 onClick="javascript:window.location = \'grups_comandes.php \'">
</p>';
} ?>

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