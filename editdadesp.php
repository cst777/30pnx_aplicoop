<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];
$superuser=strtoupper($_SESSION['user']);

$nom = $_GET['id'];

$modcdp='';
$hidden="";
$hidden2="";
$hidden3="";
$new="";


if ($nom=="" OR $nom==$user) 
{
	$nom=$user;
	$link=">><a href='families.php'>llistat famílies</a>";

}
else
{
$link=">><a href='editfamilies3.php'>crear i editar famílies</a>";
}
/* 30 Panxes */
$modcdp='<input class="button2" style="width: 180px;" type="button" value="MODIFICAR CLAU DE PAS" size="5"  onclick="javascript:window.location = \'editpass.php?id=' . $nom . '\';">';
/* end 30 Panxes */

$supernom=strtoupper($nom);

$p_tip=$_POST['tipus'];
$p_tip2=$_POST['tip2'];
$p_dia=$_POST['dia'];
$p_comp=$_POST['comp'];
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
		<meta http-equiv="content-type" content="="text/ht; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>editar dades família ::: la coope</title>
	</head>
	
<body>

<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid orange;">

<p class='path'> 
><a href='admint.php'>administració</a>
<?php echo $link; ?>
>>><a href='editdadesp.php?id=<?php echo $nom; ?>'>editar família <?php echo $supernom; ?></a>
</p>
<p class="h1" style="background: orange; text-align: left; padding-left: 20px;">
Editar família <?php echo $supernom; ?>
<span style="display: inline; float: right; text-align: center; vertical-align: middle; 
padding: 2px 50px 2px 0px;">
<?php echo $modcdp; ?>
</span>
</p>



<?php
if ($p_tip2!="" OR $p_dia!="" OR $p_comp!="" OR $p_tip!="")
	{
	$query2 = "UPDATE usuaris	
	SET tipus='".$p_tip."', tipus2='".$p_tip2."', dia='".$p_dia."', components='".$p_comp."',	
	tel1='".$p_tlf1."', tel2='".$p_tlf2."', email1='".$p_email1."', email2='".$p_email2."',
	nomf='".$p_nomf."', adressf='".$p_adressf."', niff='".$p_niff."',
	nota='".$p_nota."'
	WHERE nom='".$nom."' ";

	mysql_query($query2) or die('Error, insert query2 failed');
	
	echo "<p class='error' style='font-size: 14px;'>Els canvis en la família ".$supernom." s'han guardat correctament</p>";
	
	}
?>

<div class="contenidor_fac" style=" width:500px; border: 1px solid orange; margin-bottom:20px;">
<form action="editdadesp.php?id=<?php echo $nom; ?>" method="post" name="frmeditdadesp" id="frmeditdadesp">

<table style="padding: 10px;" width="100%" align="center" cellspading="5" cellspacing="5" >

<?php

$select= "SELECT nom,tipus,tipus2,dia,components,tel1,tel2,email1,email2,nomf,adressf,niff,nota 
FROM usuaris WHERE nom='$nom'";

$query=mysql_query($select);

if (!$query) {
    die('Invalid query: ' . mysql_error());
    }
    
list($nom,$tip,$tip2,$dia,$comp,$tlf1,$tlf2,$email1,$email2,$nomf,$adressf,$niff,$nota)=mysql_fetch_row($query);

if ($nom=="" OR $nom==$user) 
{
$hidden='<input type="hidden" name="tip2" id="tip2" value="'.$tip2.'">';
$hidden2='<input type="hidden" name="dia" id="dia" value="'.$dia.'">';
$hidden3='<input type="hidden" name="tipus" id="tipus" value="'.$tip.'">';
$new="2";
}

?>

<tr>
<td class="cos_majus">Actiu/Baixa</td>
<td class="cos">
<SELECT name="tip2<?php echo $new; ?>" id="tip2<?php echo $new; ?>" size="1" maxlength="5">

<?php
if ($tip2=='actiu') {$checked1='selected';$checked2="";}
if ($tip2=='baixa') {$checked2='selected';$checked1="";}
?>
	
<option value="actiu" <?php echo $checked1; ?>>actiu</option>
<option value="baixa" <?php echo $checked2; ?>>baixa</option>
</select>
<?php echo $hidden; ?>
</td></tr>

<tr>
<td class="cos_majus">Grup</td>
<td class="cos">
<SELECT name="dia<?php echo $new; ?>" id="dia<?php echo $new; ?>" size="1" maxlength="12" >

<?php
$select3= "SELECT nom FROM grups ORDER BY nom";
$query3=mysql_query($select3);
if (!$query3) {die('Invalid query3: ' . mysql_error());}

while (list($sgrup)=mysql_fetch_row($query3)) 
{
	if ($dia==$sgrup){echo '<option value="'.$sgrup.'" selected>'.$sgrup.'</option>';}
	else {echo '<option value="'.$sgrup.'">'.$sgrup.'</option>';}
}
?>

</select>
<?php echo $hidden2; ?>
</td></tr>
<tr>
<td class="cos_majus">Tipus d'usuari (permisos)</td>
<td class="cos">
<SELECT name="tipus<?php echo $new; ?>" id="tipus<?php echo $new; ?>" size="1" maxlength="10" >
<?php
if ($tip=='user') {$checked3='selected';$checked4="";$checked5="";$checked6="";$checked7="";$checked8="";}
elseif ($tip=='admin') {$checked3='';$checked4="selected";$checked5="";$checked6="";$checked7="";$checked8="";}
elseif ($tip=='eco') {$checked3='';$checked4="";$checked5="selected";$checked6="";$checked7="";$checked8="";}
elseif ($tip=='prov') {$checked3='';$checked4="";$checked5="";$checked6="selected";$checked7="";$checked8="";}
elseif ($tip=='cist') {$checked3='';$checked4="";$checked5="";$checked6="";$checked7="selected";$checked8="";}
elseif ($tip=='super') {$checked3='';$checked4="";$checked5="";$checked6="";$checked7="";$checked8="selected";}
echo'
<option value="user" '.$checked3.'>user</option>
<option value="admin" '.$checked4.'>admin</option>
<option value="eco" '.$checked5.'>eco</option>
<option value="prov" '.$checked6.'>prov</option>
<option value="cist" '.$checked7.'>cist</option>
<option value="super" '.$checked8.'>super</option>';
?>

</select>
<?php echo $hidden3; ?>
</td></tr>
<tr class="cos_majus">
<td>Components de la família</td>
<td>
<input type="text" name="comp" value="<?php echo $comp; ?>" size="30" maxlength="100"></td>
</tr>
<tr class="cos_majus">
<td>Telèfon 1</td>
<td>
<input type="text" name="tlf1" value="<?php echo $tlf1; ?>" size="9" maxlength="9"></td>
</tr>
<tr class="cos_majus">
<td>Telèfon 2</td>
<td>
<input type="text" name="tlf2" value="<?php echo $tlf2; ?>" size="9" maxlength="9"></td>
</tr>
<tr class="cos_majus">
<td>E-mail 1</td>
<td>
<input type="text" name="email1" value="<?php echo $email1; ?>" size="30" maxlength="50"></td>
</tr>
<tr class="cos_majus">
<td>E-mail 2</td>
<td>
<input type="text" name="email2" value="<?php echo $email2; ?>" size="30" maxlength="50"></td>
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
<textarea name="nota" cols="35" rows="4" id="nota"><?php echo $nota; ?></textarea></td>
</tr>
</table>

<p class="linia_button2" style="background: orange; text-align: center; vertical-align: middle;">
<input class="button2" type="submit" value="GUARDAR">
<input class="button2" type="button" value="SORTIR" onClick="javascript:history.go(-1);">
</p>
</div>


<p class="cos2" style="clear: both; text-align: center;">
Per canviar les dades clica el botó GUARDAR. Per modificar la clau de pas
clica el botó superior.
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