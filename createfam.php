<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];
$superuser=strtoupper($_SESSION['user']);

$p_nom=$_POST['nom'];
$p_cdp=$_POST['cdp'];
$p_cdp2=$_POST['cdp2'];
$p_tip=$_POST['tip'];
$p_tip2=$_POST['tip2'];
$p_dia=$_POST['dia'];
$p_comp=$_POST['components'];
$p_tlf1=$_POST['tlf1'];
$p_tlf2=$_POST['tlf2'];
$p_email1=$_POST['email1'];
$p_email2=$_POST['email2'];
$p_nomf=$_POST['nomf'];
$p_adressf=$_POST['adressf'];
$p_niff=$_POST['niff'];
$p_nota=$_POST['nota'];


include ('config/configuracio.php');

?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>crear dades família ::: la coope</title>
	</head>

<script>
function validate_form(){

var nom = document.getElementById("nom").value;
var cdp = document.getElementById("cdp").value;
var cdp2 = document.getElementById("cdp2").value;
var comp = document.getElementById("components").value;
var tel1 = document.getElementById("tlf1").value;
var tel2 = document.getElementById("tlf2").value;
var email1 = document.getElementById("email1").value;
var email2 = document.getElementById("email2").value;

if (nom=="") {
alert ("T'has deixat el nom en blanc"); 
document.getElementById("nom").focus();
return false;
}

var illegalChars= /[\<\>\'\;\:\\\/\"\+\!\¡\º\ª\$\|\@\#\%\¬\=\?\¿\{\}\_\[\]\(\)\.\,\-\à\á\é\è\í\ì\ó\ò\ù\ú\ü\ö\ï\ë\ä\ \&\·\*]/
if (nom.match(illegalChars)) {
   alert ('A nom: només s/accepten lletres minúscules (sense accents ni dièresi) i numeros'); 
		document.getElementById("nom").focus();		
		return false;
}

if (cdp=="") {
alert ("No has escrit cap clau de pas"); 
document.getElementById("cdp").focus();
return false;
}

if (cdp!=cdp2) {
alert ("Les claus de pas no coincideixen. \n Torna a intentar-ho"); 
document.getElementById("cdp").focus();
return false;
}

if (cdp.match(illegalChars)) {
   alert ('A Clau de Pas: només s/accepten lletres minúscules (sense accents ni dièresi) i numeros'); 
		document.getElementById("cdp").focus();		
		return false;
}

if (comp=="") {
alert ("Has deixat la casella de components de la família en blanc \n Hauries de ficar el nom de les persones que conformen la família"); 
document.getElementById("components").focus();
return false;
}

if (tel1=="" && tel2=="") {
alert ("No has escrit cap telèfon de contacte"); 
document.getElementById("tlf1").focus();
return false;
}

if (email1=="" && email2=="") {
alert ("No has escrit cap correu electrònic"); 
document.getElementById("email1").focus();
return false;
}

return true;
}
</script>

<body>

<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid orange;">

<p class='path'> 
><a href='admint.php'>administració</a>
>><a href='editfamilies3.php'>crear i editar famílies</a> 
>>><a href='createfam.php'>crear nova família</a> 
</p>
<p class="h1" style="background: orange; text-align: left; padding-left: 20px;">
Crear nova família
</p>

<div class="contenidor_fac" style=" width:500px; border: 1px solid orange; margin-bottom:20px;">
<table style="padding: 10px;" width="100%" align="center" cellspading="5" cellspacing="5" >
<?php

if ($p_nom!=""){
	$md5_cdp=md5($p_cdp);
	$query2 = "INSERT INTO usuaris (nom,claudepas,tipus,tipus2,dia,moneder,components,tel1,tel2,email1,email2,nomf,adressf,niff,nota ) 
	VALUES ('".$p_nom."', '".$md5_cdp."', '".$p_tip."', '".$p_tip2."', '".$p_dia."', '0', '".$p_comp."',
	'".$p_tlf1."', '".$p_tlf2."', '".$p_email1."', '".$p_email2."', '".$p_nomf."', '".$p_adressf."', '".$p_niff."',
	'".$p_nota."') ";

	mysql_query($query2) or die('Error, insert query2 failed:'.mysql_error());
	
	echo 
	"<tr><td align='center' class='cos2'>
	<p class='error' style='font-size: 14px;'>Una nova família s'ha introduït correctament a la base de dades:</p>
	<p>NOM: ".$p_nom."</p>
	<p>CLAU DE PAS: ".$p_cdp."</p>
	<p>PERMISOS: ".$p_tip."</p>
	<p>ACTIU: ".$p_tip2."</p>
	<p>GRUP: ".$p_dia."</p>
	<p>COMPONENTS: ".$p_comp."</p>
	<p>TELEFON 1: ".$p_tlf1."</p>
	<p>TELEFON 2: ".$p_tlf2."</p>
	<p>E-MAIL 1: ".$p_email1."</p>
	<p>E-MAIL 2: ".$p_email2."</p>
	<p>NOM FACTURA: ".$p_nomf."</p>
	<p>ADREÇA FACTURA: ".$p_adressf."</p>
	<p>NIF FACTURA: ".$p_niff."</p>
	<p>COMENTARIS: ".$p_nota."</p>
	</td></tr>
	</table>";
	
	}
else {	
?>


<form action="createfam.php" method="post" name="frmeditdadesp" id="frmeditdadesp" onSubmit="return validate_form();">

<tr class="cos_majus">
<td>Nom</td>
<td>
<input type="text" name="nom" id="nom" size="10" maxlength="10" onkeyup="frmeditdadesp.nom.value=frmeditdadesp.nom.value.toLowerCase();">
</td></tr>
<tr class="cos_majus">
<td>Clau de pas</td>
<td>
<input type="text" name="cdp" id="cdp" size="10" maxlength="10" onkeyup="frmeditdadesp.cdp.value=frmeditdadesp.cdp.value.toLowerCase();"></td>
</tr>
<tr>
<td><span class="cos_majus">Clau de pas </span><br/><span class="cos">(repeteix per seguretat)</span></td>
<td>
<input type="text" name="cdp2" id="cdp2" size="10" maxlength="10" onkeyup="frmeditdadesp.cdp.value=frmeditdadesp.cdp.value.toLowerCase();"></td>
</tr>
<tr>
<td class="cos_majus">Actiu/Baixa</td>
<td class="cos">
<SELECT name="tip2" id="tip2" size="1" maxlength="5">	
<option value="actiu" checked>actiu</option>
<option value="baixa">baixa</option>
</select></td></tr>
<tr>
<td class="cos_majus">Grup</td>
<td class="cos">
<SELECT name="dia" id="dia" size="1" maxlength="12">

<?php
$select3= "SELECT nom FROM grups ORDER BY nom";
$query3=mysql_query($select3);
if (!$query3) {die('Invalid query3: ' . mysql_error());}

while (list($sgrup)=mysql_fetch_row($query3)) 
{
	echo '<option value="'.$sgrup.'">'.$sgrup.'</option>';
}
?>

</select></td>
</tr>
<tr>
<td class="cos_majus">Tipus d'usuari (permisos)</td>
<td class="cos">
<SELECT name="tip" id="tip" size="1" maxlength="10">
<option value="user">user</option>
<option value="admin">admin</option>
<option value="eco">eco</option>
<option value="prov">prov</option>
<option value="cist">cist</option>
<option value="super">super</option>
</select></td></tr>
<tr class="cos_majus">
<td>Components de la família</td>
<td>
<input type="text" name="components" id="components" size="30" maxlength="100"></td>
</tr>
<tr class="cos_majus">
<td>Telèfon 1</td>
<td>
<input type="text" name="tlf1" id="tlf1" size="9" maxlength="9"></td>
</tr>
<tr class="cos_majus">
<td>Telèfon 2</td>
<td>
<input type="text" name="tlf2" id="tlf2" size="9" maxlength="9"></td>
</tr>
<tr class="cos_majus">
<td>E-mail 1</td>
<td>
<input type="text" name="email1" id="email1" size="30" maxlength="50"></td>
</tr>
<tr class="cos_majus">
<td>E-mail 2</td>
<td>
<input type="text" name="email2" id="email2" size="30" maxlength="50"></td>
</tr>
<tr class="cos_majus">
<td>Nom a efectes de la factura</td>
<td>
<input type="text" name="nomf" value="<?php echo $nomf; ?>" size="30" maxlength="100"></td>
</tr>
<tr class="cos_majus">
<td>Adreça a efectes de la factura</td>
<td>
<input type="text" name="adressf" value="<?php echo $adressf; ?>" size="30" maxlength="200"></td>
</tr>
<tr class="cos_majus">
<td>NIF a efectes de la factura</td>
<td>
<input type="text" name="niff" value="<?php echo $niff; ?>" size="9" maxlength="9"></td>
</tr>
<tr class="cos_majus">
<td>comentaris</td>
<td>
<textarea name="nota" cols="35" rows="4" id="nota"></textarea></td>
</tr>
</table>


<p class="linia_button2" style="background: orange; text-align: center; vertical-align: middle;">
<input class="button2" type="submit" value="GUARDAR">
<input class="button2" type="button" value="SORTIR" onClick="javascript:history.go(-1);';">
</p>
</div>

<?php
}
?>

<p class="cos2" style="clear: both; text-align: center;">
Per canviar les dades clica el botó GUARDAR
</p>
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