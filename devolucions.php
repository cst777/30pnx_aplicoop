<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) 
{

	$superuser=strtoupper($_SESSION['user']);
	$user=$_SESSION['user'];
	$sessionid = $_SESSION['sessionid'];
	
	$num=$_POST["num"];
	$unitat=$_POST["uni"];
	$pref=$_POST["ref"];

	$files = count($num);

	$ptipus=$_POST['tipus'];
	$paddfam=$_POST['nouf'];
	
	$pnumcmda=$_POST['numcmda'];
		
	$disabled="disabled";
	$disabled2="disabled";
	
	if ($ptipus!="")
	{
		$disabled="";
		if ($paddfam!="") 
		{
			$disabled2="";
		}		
	}	

	include 'config/configuracio.php';   
	
	function deleteDades($numcmda){
	// borra les dades introduides inicialment
	// abans de borrar restitueix l'estoc
	$taula33="SELECT cl.ref, p.nom, p.proveidora, p.categoria, cat.estoc, cl.cistella
	FROM comanda_linia AS cl, productes AS p, categoria AS cat 
	WHERE cl.ref=p.ref AND p.categoria=cat.tipus AND cl.numero=".$numcmda;
	$result33 = mysql_query($taula33);
	if (!$result33) {die('Invalid query33: ' . mysql_error());}
	while(list($ref,$prod,$prov,$cat,$estoc,$cistella)=mysql_fetch_row($result33))
	{
		if ($estoc=='si')
		{
			$query34= "UPDATE productes 
			SET estoc=estoc+'$cistella'
			WHERE ref='$ref'";
			mysql_query($query34) or die('Error, insert query34 failed');
		}	
	}
	$querydel="DELETE FROM comanda WHERE numero='$numcmda'";
	mysql_query($querydel) or die('Error, delete querydel failed');
	$querydel2="DELETE FROM comanda_linia WHERE numero='$numcmda'";
	mysql_query($querydel2) or die('Error, delete querydel2 failed');	
	}
	
	function selectEstoc($se_ref){
	// busquem si el producte és d'estoc o no
	$selectse= "SELECT cat.estoc FROM productes AS pr, categoria AS cat
	WHERE pr.categoria=cat.tipus AND pr.ref='$se_ref'";
	$resultse = mysql_query($selectse);
	if (!$resultse) { die('Invalid query selectse: ' . mysql_error());}
	$estocse= mysql_fetch_row($resultse);
	return $estocse;
	}
	
	function laCuenta($numero){
	/// calcula el valor total de la factura ///
	$selectCuenta = "SELECT SUM(cistella*preu*(1+iva)) FROM comanda_linia WHERE numero='".$numero."'";
	$resultCuenta = mysql_query($selectCuenta);
	if (!$resultCuenta) {die('Invalid queryCuenta: ' . mysql_error());}
	$cuenta = mysql_fetch_row($resultCuenta);
	return $cuenta;
	}

function selectNumFact($numero){
	/// Busquem el numero de factura ///
	$selectnf = "SELECT numfact FROM comanda WHERE numero='".$numero."'";
	$resultnf = mysql_query($selectnf);
	if (!$resultnf) {die('Invalid querynf: ' . mysql_error());}
	$numfact = mysql_fetch_row($resultnf);
	return $numfact;

	}

?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>crear/editar devolucions o factures fora proces ::: la coope</title>
	</head>
	<style type="text/css">
	a#color:link, a#color:visited {color:white; border: 1px solid #9cff00;}
	a#color:hover {color:black; border: 3px solid #9cff00;   -moz-border-radius: 10%;}
   a#color:active {color:white; border: 3px solid #9cff00;  -moz-border-radius: 10%;}
	</style>

	<style type="text/css" media="print">
    .NonPrintable
    {
      display: none;
    }
  </style>


<script language="javascript" type="text/javascript">

function validate_form(){
var x = new Array();
var nom = new Array();

for (i = 0; i < this.document.frmdev.elements['num[]'].length; i++){
x[i] = document.getElementById("num"+i).value;
nom[i]= document.getElementById("nom"+i).value;

if (isNaN(x[i])) {
alert ('A ' + nom[i] + ': només s/accepten numeros i el punt decimal'); 
document.getElementById("num"+i).focus();
return false;
break;
}

if (x[i]>=100 || x[i]<0) {
alert ('A ' + nom[i] + ': el numero ha de ser superior que 0 i inferior a 100'); 
document.getElementById("num"+i).focus();
return false;
break;
}

}
return true;
}

</script>

<body> 

<div class="pagina" style="margin-top: 10px;">
	<div class="contenidor_1" style="border: 1px solid #9cff00;">
	

<?php

/// En aquest punt es bifurca en dos visualitzacions:
/// la primera son els resultat a partir del submit definitiu
/// la segona el formulari

//////////////////////////////////////////////////////////////////////////////////////
///Quan li donem a acceptar fem el submit definitiu, guardem les dades de la taula///
/// i visualitzem el resultat                                                     ///
//////////////////////////////////////////////////////////////////////////////////////
	if ( isset( $_POST['acceptar'] ) ) 
	{
/// Primer hem d'estar segurs que hi ha dades num ///
	$count_files=0;
			for ($i=0; $i<$files; $i++) 
			{
				if ($num[$i] != "") 
				{
					$count_files++;
				}
			}			
			
			if ($count_files==0)
			{
				// Si no hi ha cap quantitat elegida no continua endavant //
				
				echo '<p class="error" style="padding-top: 50px;">
				No has introduït cap quantitat a cap producte!!!!
				</p>';				
				 
				die ('<p class="error" style="font-size: 14px; padding-bottom: 50px;"><a href="devolucions.php" target="cos" 
				title="clica per tornar">		
				Torna a devolució o factura fora de procés</a></p>');
			}
				//////////////////
				/// Si hi ha dades num ///
				/// creem el numero de factura o de devolució ///
				/// anotem si es factura fora de procés o devolució ///
				/// inserim les dades a la taula comandes ///
			else
			{	
				
				$data_avui=date("Y-m-d");
				
				/// creem numero factura o devolució ///
				// trobem la darrera factura de l'any vigent 
				// i creem el numero de factura següent
				// o si no existeix cap factura de l'any li donem valor 1 ///
	
				$currentyear= date("Y");
				$taulanf2 = "SELECT numfact 
				FROM comanda
				WHERE YEAR(data2)=".$currentyear."
				ORDER BY numfact DESC 
				LIMIT 1";
				$resultnf2 = mysql_query($taulanf2);
				if (!$resultnf2) {die('Invalid query: ' . mysql_error());}
				list($lastnumfact)=mysql_fetch_row($resultnf2);
	
				if ($lastnumfact!=""){$numfact=$lastnumfact+1;}
				else {$numfact=1;}
				
				/// anotem si es factura fora de procés o devolució ///
				if($ptipus=="fac") {$notes="factura fora de procés";}
				if($ptipus=="dev") {$notes="devolució";}		
						
				/// inserim les dades a la taula comanda ///
				$query2 = "INSERT INTO `comanda` ( `usuari`,`sessionid`,`data`,`check0`,`data2`,`check1`,`check2`,`numfact`,`notes`)
					VALUES ('$paddfam', '$sessionid', '$data_avui', '1', '$data_avui', '1', '1','$numfact','$notes')";
					mysql_query($query2) or die('Error, insert query2 failed');
				$numcmda=mysql_insert_id();
				
				/// visualitzem les dades ///
				/// l'usuari pot elegir entre 
				/////// acceptar-> carrega un nou formulari en blanc
				/////// editar -> carrega el formulari amb les dades introduides i es poden canviar
				/////// eliminar -> borra tot i carrega un nou formulari en blanc
	?>
		<div class="contenidor_fac">
			<form action="devolucions.php?id=1" method="post" name="frmdev2" id="frmdev2" target="cos" >
			<input type=hidden name="numcmda" value="<?php echo $numcmda; ?>">
			<input type=hidden name="tipus" value="<?php echo $ptipus; ?>">
			<input type=hidden name="nouf" value="<?php echo $paddfam; ?>">
			
			<div class="NonPrintable">
				<p class="linia_button2" style="background: #9cff00; text-align: center; vertical-align: middle;">
				<input class="button2" type="submit" name="acord" id="acord" value="D'ACORD">
				<input class="button2" type="submit" name="edit" id="edit" value="EDITAR">
				<input class="button2" type="submit" name="del" id="del" value="ELIMINAR" 
				onClick="var answer = confirm (\'Estàs segur que vols borrar aquesta factura o devolució!! \')
				if (answer)
					{document.frmdev2.submit()'}">
				<input class="button2" type="button" name="imprimir" value="IMPRIMIR" onclick="window.print();">'	
				</p>
			</div>
		</form>
			<div class="contenidor_4" style="float:left;">
				<img id="fig" style="width:175px; height:85px; padding: 10px 0px 20px 0px ;" 
				src="<?php echo $logo_factura; ?>"> 
			</div>

<?php
			/// Aconseguim les dades personals per a la factura ///
				$sel="SELECT u.nomf, u.adressf, u.niff
				FROM comanda AS c, usuaris AS u
				WHERE c.numero='$numcmda' AND c.usuari=u.nom";
				$query=mysql_query($sel) or die('query:'.mysql_error());
				list($nomf, $adressf, $niff) = mysql_fetch_row($query);
			
			/// Aconseguim la data d'avui per veure ///
				$ver_avui = date("d-m-Y");
				$year_avui=date("Y");
?>

			<div style="width: 510px; float:right;">
				<p class="cos16" 
				style="font-weight: bold; text-align: left; padding-left:40px; padding-right:25px; 
					 vertical-align: middle;">
				<span style="color: grey;">Factura nº </span><?php echo $numfact."/".$year_avui; ?>
				<br/>
				<span style="color: grey;">Data: </span><?php echo $ver_avui; ?>
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
			/// entrem les quantitats a comanda_linia ///
				for ($i=0; $i<$files; $i++) 
				{
					if ($num[$i] != "" AND $num[$i] != 0) 
					{
					/// Busquem ref,preu, iva, marge i descompte a partir de nomprod i nomprov ////
						$query0= "SELECT nom, proveidora, preusi, iva, marge, descompte FROM productes 
						WHERE ref='$pref[$i]'";
						$result0=mysql_query($query0);
						if (!$result0) { die("Query0 to show fields from table failed");}
						list($nomprod,$nomprov,$spreusi,$siva,$smarge,$sdescompte)=mysql_fetch_row($result0);
											
						$pvp=$spreusi*(1+$smarge);
						$pvp=sprintf("%01.2f", $pvp);
								
					/// Si es una devolució la quantitat demanada és negativa ///
						if($ptipus=="dev") {$num[$i]=-$num[$i];}
								
					/// entrem les quantitats a comanda_linia ///
						$query4 = "INSERT INTO `comanda_linia` ( `numero`, `ref`, `cistella`, `preu`, `iva`, `descompte`)
						VALUES ('$numcmda', '$pref[$i]', '$num[$i]', '$pvp', '$siva', '$sdescompte')";
						mysql_query($query4) or die('Error, insert query failed');
					
					/// restem les quantitats de l'estoc en el cas que la categoria del producte sigui d'estoc///
						$estocse=selectEstoc($pref[$i]);
						$estocse=$estocse[0];
						if ($estocse=='si')
						{
							$query7= "UPDATE productes 
							SET estoc=estoc-'$num[$i]'
							WHERE ref='$pref[$i]'";
							mysql_query($query7) or die('Error, update query7 failed');
						}	
					}
				}	
			///////////////////////	
			/// Les visualitzem ///
			//////////////////////
				$sel5="SELECT cl.ref, prod.nom, prod.proveidora, prod.unitat, cl.cistella, cl.preu, cl.descompte, cl.iva
				FROM comanda_linia AS cl, productes AS prod
				WHERE cl.numero='$numcmda' AND cl.ref=prod.ref
				ORDER BY prod.categoria, prod.proveidora, prod.nom";
				$result5=mysql_query($sel5) or die(mysql_error());

				$total=0; $total_import_brut=0; $totaliva=0;
				while (list ($ref, $nomprod, $nomprod2, $unitat, $cistella, $preu, $descompte, $iva)=mysql_fetch_row($result5)) 
				{
				/// agafem la primera lletra de la unitat ///
					$unitat1=substr($unitat,0,1);
			
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
				<td align="center"><?php echo $total; ?>&#8364;</td>
		</tr>
		</table>
	</div>
	<p class="cos2" style="clear: both; text-align: left;">
	Les teves dades procedeixen d’un fitxer del que és propietari i responsable aquesta entitat, 
	davant la qual poden exercitar els drets d’accés, rectificació, cancel•lació i oposició 
	reconeguts per la LO 15/1999, de 13 de desembre, de protecció de dades de caràcter personal.
	</p>
	</div>
	
<?php
	
			}
		}
		else 
		{
		
/////////////////////////
/// A partir d'aquí vé el formulari  ///////
//////////////////////////////////
?>

	<p class='path'> 
	><a href='admint.php'>administració</a>  
	>><a href='devolucions.php'>Crear devolucions o factures fora procés </a>
	</p>

	<p class="h1" style="background: #9cff00; text-align: left; padding-left: 20px;">
	Crear devolucions o factures fora procés</p>

<div class="contenidor_fac" style="border: 1px solid green; max-height: 350px; width: 450px;">

<?php
	////////////////////////////
	/// Si apretem el botó D'ACORD des de la visualització de la factura ///
	/// es carrega al moneder el valor total de la factura o de la devolució ///
	/// Sino conitnua endavant ///
	///////////////////////////////
	if( isset($_POST['acord'])) 
	{
		$cuenta=laCuenta($pnumcmda);
		///El valor total de la factura ha de canviar de signe
		/// ja que es resta del moneder
		$cuenta=-$cuenta[0];
		$cuenta=sprintf("%01.2f", $cuenta);
		$numfact=selectNumFact($pnumcmda);
		$numfact=$numfact[0];
		$session = date ("Y-m-d H:i:s");
		$selectMoneder2 = "INSERT INTO moneder(sessio, user, data, familia, concepte, valor) 
		VALUES ('".$session."','".$user."','".date('Y-m-d')."','".$paddfam."','Factura num. ".$numfact."','".$cuenta."')";
		$resultMoneder2 = mysql_query($selectMoneder2);
		if (!$resultMoneder2) {die('Invalid query: ' . mysql_error());}
		if ($ptipus=="dev"){$text=array("retornat","devolució");}
		if ($ptipus=="fac"){$text=array("carregat","factura");}
		$yearfact=date('Y');
		die ('<p class="error" style="font-size: 14px; padding-bottom: 50px;">
		S\'han '.$text[0].' '.$cuenta.'€ al moneder de la família '.$paddfam.' corresponents a la '.$text[1].' numero '.$numfact.'/'.$yearfact.'</p>
		<p class="error" style="font-size: 14px; padding-bottom: 50px;">
		<a href="devolucions.php" target="cos" title="clica per tornar"> Torna a devolució o factura fora de procés</a></p>');
	}
	/////////////////////
	/// Si apretem el boto ELIMINAR des de la visualització de la factura///
	/// s'eliminen les dades i apareix un comentari al respecte ///
	/// Abans de borrar les dades restituim la quantitat a estoc
	/// Si no continua endavant ///
	////////////////////
	if ( isset( $_POST['del'] ) ) 
	{
		deleteDades($pnumcmda);
		die ('<p class="error" style="font-size: 14px; padding-bottom: 50px;">
		Les dades introduides de la factura o devolució s\'han borrat correctament</p>');
	}
	///////////////////////////////////////////////////////////////////////////
?>

<form action="" method="post" name="frmdev" id="frmdev" onSubmit="return validate_form()" target="cos" >

<table width="60%" align="center" valign="middle" cellpadding="5" cellspacing="5">

<tr>
<td align="center" class='cos_majus'>Tipus</td>
<td align="center" class="cos">
<SELECT class="button2" name="tipus" id="tipus" size="1" maxlength="30" onchange="document.frmdev.submit()">
<option value="">Elegeix tipus</option>
<?php
		$selected=""; $selected1="";
		if($ptipus=="dev") { $selected="selected";} 
		if($ptipus=="fac") { $selected1="selected";}
?>
<option value="dev" <?php echo $selected; ?>>Devolució</option>
<option value="fac" <?php echo $selected1; ?>>Factura</option>
</select>
</td>

<td align="center" class='cos_majus'>Família</td>
<td align="center" class="cos">
<SELECT class="button2" name="nouf" id="nouf" size="1" maxlength="30" <?php echo $disabled; ?> onchange="document.frmdev.submit()">
<option value="">insereix familia</option>

<?php
		// Es pot elegir entre totes les famílies actives o anònim//
		$selected2="";		
		if($paddfam=="anom") { $selected2="selected";} 
		echo '<option value="anom" '.$selected2.'>Anònim</option>';
		
		$taula7="SELECT nom FROM usuaris WHERE tipus2='actiu' ORDER BY nom";
		$result7 = mysql_query($taula7);
		if (!$result7) {die('Invalid query7: ' . mysql_error());}

		while(list($sfam)=mysql_fetch_row($result7))
		{
			$selected3="";		
			if ($paddfam==$sfam) { $selected3="selected";} 
			echo '<option value="'.$sfam.'" '.$selected3.'>'.$sfam.'</option>';
			
		}
?>

</select>
</td>
</tr>
</table>
</div>

<p class="h1" style="background: #9cff00; text-align: left; padding-left: 20px;">
Anar a Categoria:
<SELECT class="button2" name="cat" id="cat" size="1" maxlength="30" <?php echo $disabled2; ?>
		onChange="location=this.form.cat.value">
<option value="">elegeix categoria</option>

<?php		

		$sel = "SELECT tipus FROM categoria WHERE actiu='activat' ORDER BY tipus ASC";
		$result = mysql_query($sel);
		if (!$result) {die('Invalid query: ' . mysql_error()); }
		
		while (list($scat)= mysql_fetch_row($result))
		{
			if ($pcat==$scat)
				{
					echo '<option value="#'.$scat.'" selected>'.$scat.'</option>';
				}
				else 
				{
					echo '<option value="#'.$scat.'">'.$scat.'</option>';
				}	
		}
?>

</select>
</p>



<div id="contenidor_1" style="height: 250px; clear: both; overflow: scroll; overflow-x: hidden; ">

		<?php

		$sel = "SELECT tipus FROM categoria WHERE actiu='activat' ORDER BY tipus ASC";
		$result = mysql_query($sel);
		if (!$result) {die('Invalid query: ' . mysql_error()); }

		$color=array("#C0C000","#00b2ff","orange","#b20000","#14e500","red","#8524ba","green");
		$id=0;
		$cc=0;
		while (list($sscat)= mysql_fetch_row($result))
		{
			print ('<a name="'.$sscat.'"></a> 
					<p class="h1" style="background: '.$color[$cc].'; text-align: left; padding-left: 20px;">'.$sscat.'</a></p>');
			$cc++;
			if ($cc==7){$cc=0;}

			$sel2 = "SELECT pr.ref,pr.nom,pr.unitat,pr.proveidora,ctg.tipus,ctg.estoc,pr.subcategoria,pr.preusi,pr.iva,
			pr.marge, pr.descompte,pr.estoc FROM productes AS pr, categoria AS ctg
			WHERE pr.categoria=ctg.tipus AND pr.categoria='$sscat' AND pr.actiu='actiu'
			ORDER BY pr.categoria, pr.nom ";

			$result2 = mysql_query($sel2);
			if (!$result2) { die('Invalid query2: ' . mysql_error()); }

			print ('<table align="centre" width="100%" class="cos" 
			style="padding-left: 30; padding-right: 30; ">
			<tr>');

			$contador=0;
			while(list($ref,$nomprod,$unitat,$prov,$categ,$ctg_estoc,$subcat,$preu,$iva,$marge,$descompte,$pr_estoc) = mysql_fetch_row($result2)) 
			{
				//// Si estem editant un formulari ja fet -existeix $pnumcmda-
				/// Hem apretat el boto EDITAR a la visualització de la factura
				/// han d'apareixer inicialment les quantitats elegides
				//// en un principi $num, sino num="" ////////////
				if (isset($_POST['edit']))
				{
					$sel3 = "SELECT cistella FROM comanda_linia 
					WHERE numero='$pnumcmda' AND ref='$ref'";

					$result3 = mysql_query($sel3);
					if (!$result3) { die('Invalid query3: ' . mysql_error()); }
					list ($quantitat) = mysql_fetch_row($result3);
										
					if ($quantitat!="")
					{
					/// per veure la quantitat amb els decimals imprescindibles /////
						$r2=round($quantitat, 2)*1000;
						$r1=round($quantitat, 1)*1000;
						$r0=round($quantitat)*1000;
						$rb=$quantitat*1000;
						if ($rb==$r0) 
						{
							$nd=0;
						}
						else 
						{
							if ($rb==$r1) {$nd=1;}
							else 
							{
								if ($rb==$r2) {$nd=2;}
								else {$nd=3;}
							}
						}
						$num[$id]=round($quantitat, $nd);
						if($ptipus=="dev") {$num[$id]=-$num[$id];}
						
						/// recarreguem l'estoc ///				
						if ($ctg_estoc=='si')
						{
							$query34= "UPDATE productes 
							SET estoc=estoc+'$quantitat'
							WHERE ref='$ref'";
							mysql_query($query34) or die('Error, insert query34 failed');
							
							$sel35 = "SELECT estoc FROM productes WHERE ref='$ref'";
							$result35 = mysql_query($sel35);
							if (!$result35) { die('Invalid query35: ' . mysql_error()); }
							$pr_estoc = mysql_fetch_row($result35);
							$pr_estoc=$pr_estoc[0];
						}	
					}
					//////////////////////////////////////
				}
				
				//// En els productes d'estoc, apareix l'estoc ////
				//// Si l'estoc es negatiu apareix en gris //// 
				if ($ctg_estoc=='si') 
				{
					$rpr_estoc=round($pr_estoc,1); 
					$w_estoc="[".$rpr_estoc."]";
					if ($pr_estoc<=0) {$color_cos="color: grey;";}
					else {$color_cos="";}
				}
				else {$w_estoc="";}				
				
				//// càlcul del pvp ///
				/// inclou iva i marge, però no descompte ////
				$pvp=$preu*(1+$iva)*(1+$marge);
				$pvp=sprintf("%01.2f", $pvp);
				
				//// si existeix un descompte apareix en vermell //// 
				$w_desc="";				
				if ($descompte!=0)
				{
					$descompte=$descompte*100;
					$w_desc="<span style='color:red; text-decoration:blink' > descompte:".$descompte."%</span>";
				}
								
				print('<td width="6%">
		      <input align="right" name="num[]" id="num'.$id.'" type="TEXT" value="'.$num[$id].'" maxlength="5" size="3" '.$disabled2.'>				
				</td>
				<td width="26%" style="'.$color_cos.'">
            '.$nomprod.' ('.$pvp.' &#8364;/'.$unitat.') '.$w_estoc.' '.$w_desc.'
            <input type=hidden name="ref[]" value="'.$ref.'">
             <input type=hidden name="uni[]" value="'.$unitat.'">
             <input type=hidden name="nomp[]" id="nom'.$id.'" value="'.$nomprod.'">
            </td>');
                      
				$contador++;
				$id++;

				if ($contador==3) 
				{
					print ('</tr><tr>');
					$contador=0;
				}

			}

			print ('</tr></table>');
		}
		
		/// Si hem apretat el botó EDITAR de la visualització de la factura
		/// existeix $pnumcmda vol dir que es una segona o posterior edició
		/// Fem un input invisible amb $pnumcmda 
		/// borrem les dades existents amb el numero de comanda conegut ($pnumcmda)///
		if($_POST['edit']) 
		{
			echo '<input type="hidden" name="numcmda" id="numcmda" value="'.$pnumcmda.'">';
			
			$querydel="DELETE FROM comanda WHERE numero='$pnumcmda'";
			mysql_query($querydel) or die('Error, delete querydel failed');
			$querydel2="DELETE FROM comanda_linia WHERE numero='$pnumcmda'";
			mysql_query($querydel2) or die('Error, delete querydel2 failed');	
		}
?>

		</div>
		<p class="linia_button2" style="background: #9cff00; text-align: center; vertical-align: middle;">
		<input class="button2" name="acceptar" type="submit" id="acceptar" value="Acceptar" <?php echo $disabled2; ?>>
		<input class="button2" type="button" value="Sortir" onClick="javascript:location.href='admint.php'">
		</p>	
		</form>
		<p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
		Omple les quantitats dels productes que vulguis i clica ACCEPTAR. 
		Les devolucions s'introdueixen en positiu encara que sigui un retorn.
		Els productes estan ordenats per categoria. Només apareixen els productes actius i les categories actives. 
		Per buscar un producte concret pots utilitzar l'opció de recerca del teu navegador (usualment al menu Editar>opció Buscar)
		</p>
	</div>
</div>

<?php
	}
	include 'config/disconect.php';
} 
else 
{
	header("Location: index.php"); 
}
?>	