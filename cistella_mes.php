	<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' OR $_SESSION['codi_cistella'] != 'in') 
{
	$user = $_SESSION['user'];
	
	$gref=$_GET['id'];
	$gdata=$_GET['id3'];
	$gcat=$_GET['id4'];
	$gvis=$_GET['id5'];
	$gproces=$_GET['id6'];
	$ggrup=$_GET['id7'];
	$gcist2=$_GET['id8'];
	$gfam=$_GET['id9'];
	
	$paddfam=$_POST['nouf'];
	$pprov=$_POST['prov'];
	$pprod=$_POST['prod'];
	$pref=$_POST['ref'];
	$pnum=$_POST['num'];
	
	list($mdiatdx, $mestdx, $anytdx ) = explode("-", $gdata);
	$gbd_data=$anytdx."-".$mestdx."-".$mdiatdx;

	include 'config/configuracio.php';

	/// Busquem nomprod i nomprov a partir de gref ////
	$query0= "SELECT nom, proveidora FROM productes WHERE ref='$gref'";
	$result0=mysql_query($query0);
	if (!$result0) { die("Query0 to show fields from table failed");}

	list($gnomprod,$gprov)=mysql_fetch_row($result0);
	///////////	
	
	$nota="";
	
	////////////////////////////////////////////
	// si hi ha dades GET id ($gref) vol dir que prové de cistella_prod.php //
	// i es vol afegir una família al producte ///////////////////////////////
	///////////////////////////////////////////
	// si no pot procedir de cistella2_fam.php (gfam GET id9 existeix) ///
	// i es vol afegir un producte quan ja tenim la família ///
	// o de cistelles.php o cistelles2.php i es vol afegir un producte //
	// (associat a una família i comanda) a un proces-grup ///////////////////
	/////////////////////////////////////////////////////////////////////////
	
	$title_array=array("Introduir nova família","Afegir nou producte");
	$a="id=".$gref;
	$form_action_pre_array=array("cistella_mes.php?","id=$gref",
		"&id3=".$gdata."&id4=".$gcat."&id5=".$gvis."&id6=".$gproces."&id7=".$ggrup."","&id8=1","&id9=$gfam",";");
	if ($gref!="") 
	{
		/// Valors si procedim de cistella_prod.php///
		$link_cap=">>>><a href='cistella_prod.php?id=".$gref."&id3=".$gdata."&id4=".$gcat."&id5=".$gvis."&id6=".$gproces."
			&id7=".$ggrup."'>".$gnomprod."</a>";
		$title=$title_array[0];
		$form_action_pre=$form_action_pre_array[0].$form_action_pre_array[1].$form_action_pre_array[2];
		$form_action="cistella_prod.php?id=".$gref."&id3=".$gdata."&id4=".$gcat."&id5=".$gvis."&id6=".$gproces."&id7=".$ggrup;
		$subtit='Elegeix una família i clica ACCEPTAR.';
		$disabled3="disabled";
		if ($paddfam!=""){$disabled3="";}
		$sortir="cistella_prod.php?id=".$gref."&id3=".$gdata."&id4=".$gcat."&id5=".$gvis."&id6=".$gproces."&id7=".$ggrup;
		$idcist2="";
	}
	else 
	{
		if($gfam!="")
		{
			///Valors quan provenim de cistella2_fam.php ///
			$procedencia="cistella2_fam.php?id=".$gfam."&id2=".$gdata."&id3=".$gproces."&id4=".$ggrup."&id5=".$gvis;
			$title=$title_array[1];	
			$link_cap=">>>><a href='".$procedencia."'>"
			.$gfam."</a>";
			$form_action_pre=$form_action_pre_array[0].$form_action_pre_array[2].$form_action_pre_array[4];
			$form_action=$procedencia;
			$subtit='Elegeix una proveïdora i un producte, en aquest ordre, i clica ACCEPTAR.';
			$disabled2="disabled";
			$disabled3="disabled";
			if ($pprov!="") 
			{
				$disabled2="";
				if ($pprod!="") 
				{
					$disabled3="";
				}		
			}
			$sortir=$procedencia;
			$idcist2="2";
		}
		else
		{
			if($gcist2!="")
			/// Valors quan procedim de cistelles2.php ///
			{
				$idcist2="2";
				$title=$title_array[0];$form_action_pre=$form_action_pre_array[0].$form_action_pre_array[2].$form_action_pre_array[3];
			}
			else
			{
				/// Valors quan procedim de cistelles.php ///
				$idcist2="";
				$title=$title_array[1];$form_action_pre=$form_action_pre_array[0].$form_action_pre_array[2];
			}
			/// Valors per cistelles.php i cistelles2.php///
			$link_cap="";
			$disabled="disabled";
			$disabled2="disabled";
			$disabled3="disabled";
			$form_action="cistelles".$idcist2.".php?id2=".$gdata."&id3=".$gproces."&id4=".$ggrup."&id5=".$gvis;
			$subtit='Elegeix una família, una proveïdora i un producte, en aquest ordre, i clica ACCEPTAR.';			
			$sortir="cistelles".$idcist2.".php?id2=".$gdata."&id3=".$gproces."&id4=".$ggrup."&id5=".$gvis;
			if ($paddfam!="")
			{
				$disabled="";
				if ($pprov!="") 
				{
					$disabled2="";
					if ($pprod!="") 
					{
						$disabled3="";
					}		
				}		
			}	
		}	
	}
	?>
	

	<html>
		<head>
			<meta http-equiv="content-type" content="text; charset=UTF-8" >
			<link rel="stylesheet" type="text/css" href="coope.css" />			
			<title>introduir novetats cistella ::: la coope</title>
			
			<style type="text/css">
			a#color:link, a#color:visited {color:white; border: 1px solid #9cff00;}
			a#color:hover {color:black; border: 1px solid #9cff00;   -moz-border-radius: 10%;}
   		a#color:active {color:white; border: 1px solid #9cff00;  -moz-border-radius: 10%;}
   		a#color2:link, a#color2:visited, a#color2:hover, a#color2:active {color:black;}
			</style>
		</head>

<body>

<div class="pagina" style="margin-top: 10px;">

<div class="contenidor_1" style="border: 1px solid green;">
<p class='path'> 
><a href='admint.php'>administració</a> 
>><a href='grups_comandes.php'>grups de comandes i cistelles</a>
>>><a href='cistelles<?php echo $idcist2; ?>.php?id2=<?php echo $gdata."&id3=".$gproces."&id4=".$ggrup."&id5=".$gvis; ?>'>
fer la cistella <?php echo $gdata." - ".$gproces." - ".$ggrup; ?> </a>
<?php echo $link_cap; ?> 
</p>
<p class="h1" style="background: green; text-align: left; padding-left: 20px;"><?php echo $title; ?></p>

<div class="contenidor_fac" style="border: 1px solid green; max-height: 350px; overflow: scroll; overflow-x:hidden;">

<form action="<?php echo $form_action_pre; ?>" method="post" name="frmmes" id="frmmes"  target="cos">
	
<table width="60%" align="center" valign="middle" cellpadding="5" cellspacing="5">

<tr class='cos_majus'>
<td align="center">Familia</td>
<td align="center">Proveïdora</td>
<td align="center">Producte</td>
</tr>

<tr class="cos">
<td align="center">

<?php
	if ($gfam!="") 
	{
		echo $gfam;
	}
	else 
	{
		echo'<SELECT class="button2" name="nouf" id="nouf" size="1" maxlength="30" onchange="document.frmmes.submit()">
		<option value="">insereix familia</option>';

		// Es pot elegir entre totes les famílies actives del grup //
		
		$taula7="SELECT u.nom, c.numero, cl.ref FROM usuaris AS u LEFT JOIN comanda AS c 
		ON u.nom=c.usuari AND c.data='$gbd_data' AND c.proces='$gproces' AND c.grup='$ggrup'
		LEFT JOIN comanda_linia AS cl ON c.numero=cl.numero AND cl.ref='$gref'
		WHERE u.dia='$ggrup' AND u.tipus2='actiu' 
		ORDER BY u.nom";
		$result7 = mysql_query($taula7);
		if (!$result7) {die('Invalid query7: ' . mysql_error());}

		while(list($sfam,$snum,$sref)=mysql_fetch_row($result7))
		{
			if($sref=="") 
			{
				$text="";
				if($snum!="") 
				{
					$text=$sfam.'('.$snum.')';
				}
				else 
				{
					$text=$sfam; 
				}
				
				if ($paddfam==$sfam)
				{
					echo '<option value="'.$sfam.'" selected>'.$text.'</option>';
					$input_num=$snum;				
				}
				else 
				{
					echo '<option value="'.$sfam.'">'.$text.'</option>';
				}	
			}
		}
		echo '</select>';
	}
?>

</td>

<td align="center">
<?php
	if ($gref!="") 
	{
		echo $gprov;
	}
	else 
	{
		echo '<SELECT name="prov" id="prov" size="1" maxlength="30" '.$disabled.' onChange="document.frmmes.submit();">
			<option value="">elegeix una proveïdora</option>';

		$query= "SELECT nom FROM proveidores ORDER BY nom";
		$result=mysql_query($query);
		if (!$result) {die("Query to show fields from table failed");}

		while (list($sprov)=mysql_fetch_row($result)) 
		{
			if ($pprov==$sprov){echo '<option value="'.$sprov.'" selected>'.$sprov.'</option>';}
			else {echo '<option value="'.$sprov.'">'.$sprov.'</option>';}
		}
		echo '</SELECT>';
	}
?>

</td>

<td align="center">
<?php
	if ($gref!="") 
	{
		echo $gnomprod;
	}
	else 
	{
		echo '<SELECT name="prod" id="prod" size="1" maxlength="30" '.$disabled2.' onchange="document.frmmes.submit()">
			<option value="">elegeix un producte</option>';
			
		// Es pot elegir entre tots els productes excepte els que ja hi son ///
		
		$query2 = "SELECT pr.ref, pr.nom FROM productes AS pr	WHERE pr.proveidora='$pprov' ORDER BY pr.nom";
		$result2=mysql_query($query2);
		if (!$result2) {die("Query2 to show fields from table failed:" . mysql_error());}

		while (list($sref,$sprod)=mysql_fetch_row($result2)) 
		{
			$query3 = "SELECT cl.ref, pr.nom FROM comanda_linia AS cl, comanda AS c, productes AS pr
			WHERE c.numero=cl.numero AND cl.ref=pr.ref AND cl.ref='$sref'
			AND c.data='$gbd_data' AND c.proces='$gproces' AND c.grup='$ggrup' 
			GROUP BY cl.ref";
			$result3=mysql_query($query3);
			if (!$result3) {die("Query3 to show fields from table failed:" . mysql_error());}
			
			list($r, $ssprod)=mysql_fetch_row($result3);
			if(!$ssprod) 
			{
				if ($pprod==$sprod)
				{
					echo '<option value="'.$sprod.'" selected>'.$sprod.'</option>';
					$input_ref=$sref;
				}
				else {echo '<option value="'.$sprod.'">'.$sprod.'</option>';}	
			}
		}
		echo '</SELECT>';
	}
?>
</td>
</tr>

</table>	

</div>
</form>
<form action="<?php echo $form_action; ?>" method="post" name="frmmes2" id="frmmes2"  target="cos" >
<p class="linia_button2" style="background: green; text-align: center; vertical-align: middle;">
<input type="hidden" name="nouf" id="nouf" value="<?php echo $paddfam; ?>">
<input type="hidden" name="prov" id="prov" value="<?php echo $pprov; ?>">
<input type="hidden" name="prod" id="prod" value="<?php echo $pprod; ?>">
<input type="hidden" name="ref" id="ref" value="<?php echo $input_ref; ?>">
<input type="hidden" name="num" id="num" value="<?php echo $input_num; ?>">

<input class="button2" name="acceptar" type="button" value="Acceptar" <?php echo $disabled3; ?> onclick="document.frmmes2.submit()">

<input class="button2" type="button" value="Sortir" onClick="location.href='<?php echo $sortir; ?>'">
</p>

</form>

<p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
<?php echo $subtit; ?>
</p>

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


