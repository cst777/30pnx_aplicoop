<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

	$sessionid=$_SESSION['sessionid'];

	$gprodref=$_GET['id'];
	$gdata=$_GET['id3'];
	$gcat=$_GET['id4'];
	$gvis=$_GET['id5'];
	$gproces=$_GET['id6'];
	$ggrup=$_GET['id7'];
	$gnumfact=$_GET['id8'];
	
	$paddfam=$_POST['nouf'];
	$pnum=$_POST['num'];
	
	// si hi ha un numero comanda de factura //
	if ($gnumfact!=""){$aw='AND c.numero='.$gnumfact; $id6='&id6='.$gnumfact; $id8='&id8='.$gnumfact;}
	else {$aw=""; $id6=""; $id8="";}
	//	
	
	if ($_SESSION['codi_cistella'] != 'in')
	{
		$gvis=0;
	}
	
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
		$button='<input class="button2" name="acceptar" type="submit" value="Acceptar">';
		$sty="";
		$intronovafam='<input class="button2" style="width:160px;" type="button" value="INTRODUIR NOVA FAMÍLIA" 
		onClick="javascript:window.location = \'cistella_mes.php?id='.$gprodref.'&id3='.$gdata.'&id4='.$gcat.'&id5='
		.$gvis.'&id6='.$gproces.'&id7='.$ggrup.'\'">';
	}

	list($mdiatdx, $mestdx, $anytdx ) = explode("-", $gdata);
	$gbd_data=$anytdx."-".$mestdx."-".$mdiatdx;

	include 'config/configuracio.php';
	
	/// Busquem nomprod i nomprov a partir de prodref ////
	$query0= "SELECT nom, proveidora FROM productes WHERE ref='$gprodref'";
	$result0=mysql_query($query0);
	if (!$result0) { die("Query0 to show fields from table failed");}

	list($gnomprod,$gprov)=mysql_fetch_row($result0);
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
				VALUES ('$pnum', '$gprodref', '1', '0')";
			mysql_query($query2) or die('Error, insert query2 failed');
		}
		else 
		{
			$query3 = "INSERT INTO comanda ( `usuari` , `proces`, `grup`, `sessionid` , `data` )
				VALUES ('$paddfam', '$gproces', '$ggrup', '$sessionid', '$gbd_data')";
			mysql_query($query3) or die('Error, insert query3 failed');
			$inumcmda=mysql_insert_id(); 		

			$query4 = "INSERT INTO comanda_linia (numero, ref, quantitat, cistella)
				VALUES ('$inumcmda', '$gprodref', '1', '0')";
			mysql_query($query4) or die('Error, insert query4 failed'); 	
		}	
	}	
	
///Inici html///
?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />			
		<title>fer la cistella - productes ::: la coope</title>
</head>

<script language="javascript" type="text/javascript">

function validate_form(){

var x = new Array();
var nom = new Array();
var answered;
var i;
var error = "Les següents families tenen la cistella buida:\n\n";
var a = "";

/// la funció lenght no detecta arrays d'un item. Així pots solucionar-se el problema ///
var len= this.document.frmComanda.elements['num[]'].length;
if(len == undefined) len = 1;

for (i = 0; i < len; i++){
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

if (x[i]=="" || x[i]==0) {
a += nom[i] + '\n';
}

}

if (a != "") {
a += '\nCancelar: Tornar a la pàgina per omplir les cistelles buides \nAcceptar: Continuar endavant';
var answered = confirm(error + a);
	if (answered){
		return true;
	}
	else{
      return false;
	}
}
return true;
}

</script>


<body>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid green;">
<p class='path'> 
><a href='admint.php'>administració</a> 
>><a href='grups_comandes.php'>grups de comandes i cistelles</a>
>>><a href='cistelles.php?id2=<?php echo $gdata."&id3=".$gproces."&id4=".$ggrup."&id5=".$gvis.$id6; ?>#<?php echo $gcat; ?>'>cistella <?php echo $gdata." - ".$gproces." - ".$ggrup; ?></a>  
>>>><a href='cistella_prod.php?id=<?php echo $gprodref."&id3=".$gdata."&id4=".$gcat."&id5=".$gvis."&id6=".$gproces."&id7=".$ggrup.$id8; ?>'> <?php echo $gnomprod."-".$gprov; ?></a>
</p>
<p class="h1" style="background: green; text-align: left; padding-left: 20px;">cistella <?php echo $gdata." - ".$gproces." - ".$ggrup; ?></p>
<div class="contenidor_fac" style="border: 1px solid green; max-height: 350px; overflow: scroll; overflow-x:hidden;">
<p class="h1" style="background: green; text-align: left; padding-left: 20px;">
<?php echo $gnomprod."-".$gprov; ?>
<span style="display: inline; float: right; text-align: center; vertical-align: middle; padding: 2px 50px 2px 0px;">
<?php echo $intronovafam; ?>
</span>
</p>

<?php
	////Busquem dades sobre el producte en qüestió////
	$query= "SELECT unitat,preusi,iva,marge,descompte 
	FROM productes 
	WHERE ref='$gprodref'";
	$result=mysql_query($query);
	if (!$result) { die("Query to show fields from table failed");}

	list($unitat,$preusi,$iva,$marge,$descompte)=mysql_fetch_row($result);
	/// el pvp sense iva -pvpsi- és el preu més el marge menys el descompte ///
	/// el pvp amb iva -pvp- el el pvpsi més l'iva ///
	$pvpsi=$preusi*(1+$marge);
	$pvpsi=sprintf("%01.2f", $pvpsi);
	$pvp=$pvpsi*(1+$iva);
	$pvp=sprintf("%01.2f", $pvp);
	/// marge, iva i descompte els visualitzem en % /// 
	$vis_marge=$marge*100;
	$vis_iva=$iva*100;
	$vis_descompte=$descompte*100;
	
?>
<form action="cistelles.php?id=<?php echo $gprodref.'&id2='.$gdata.'&id3='.$gproces.'&id4='.$ggrup.'&id5='.$gvis.$id6; ?>#<?php echo $gcat; ?>" 
 method="post" name="frmComanda" id="frmComanda"  target="cos" onSubmit="return validate_form();" >

<table width="100%" align="center" valign="left" cellpadding="5" cellspacing="5">

<tr>
<td class="cos" align="center" style="border: 1px solid green;">
PVP sense iva: <?php echo $pvpsi; ?> €/<?php echo $unitat; ?> 
<span style="color: grey;">[Preu:<?php echo $preusi; ?>]+[Marge:<?php echo $vis_marge; ?>%]</span>
PVP amb iva: <?php echo $pvp; ?> €/<?php echo $unitat; ?> <span style="color: grey;">[Iva: <?php echo $vis_iva; ?>%] </span>
Descompte: <?php echo $vis_descompte; ?>%
<br><a class="button2" href="canvi_massiu_productes.php?id=<?php echo $gprodref.'&id3='.$gdata.'
&id4='.$gcat.'&id5='.$gvis.'&id6='.$gproces.'&id7='.$ggrup; ?>" style="width: 120px;">Canvi dades</a>
</td></tr></table>

<table width="60%" align="center" valign="middle" cellpadding="5" cellspacing="5">

<tr class='cos_majus'><td>Familia</td>
<td align="center">Comanda</td>
<td align="center">Cistella</td>
</tr>

<?php

	$taula3 = "SELECT cl.numero, c.usuari, cl.ref, cl.quantitat, cl.cistella 
	FROM comanda_linia AS cl, comanda AS c
	WHERE cl.numero=c.numero AND cl.ref='$gprodref' AND c.data='$gbd_data' 
	AND c.proces='$gproces' AND c.grup='$ggrup' ".$aw."
	ORDER BY c.usuari";
	
	$result3 = mysql_query($taula3);
	if (!$result3) {die('Invalid query3: ' . mysql_error());}

	$i=0;
	while(list($numero,$familia,$p,$quantitat,$cistella)=mysql_fetch_row($result3))
	{

?>


<tr class="cos"><td><?php echo $familia; ?></td>

<td align="center"><?php echo $quantitat; ?></td>

<td align="center">
		<input align="right" name="num[]" id="num<?php echo $i; ?>" type="TEXT" maxlength="7" size="5" 
		value="<?php echo $cistella; ?>" <?php echo $readonly; ?> >
    <input type="hidden" name="nom[]" id="nom<?php echo $i; ?>" value="<?php echo $familia; ?>">
    <input type="hidden" name="numcmda[]" id="numcmda<?php echo $i; ?>" value="<?php echo $numero; ?>">

</td>
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

<input class="button2" type="button" value="Sortir" 
	onClick="location.href='cistelles.php?id2=<?php echo $gdata."&id3=".$gproces."&id4=".$ggrup."&id5=".$gvis.$id6."#".$gcat; ?>'">
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