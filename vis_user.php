<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

$nom = $_GET['id'];
$supernom=strtoupper($nom);

include 'config/configuracio.php';

$select= "SELECT nom,tipus,tipus2,dia,components,tel1,tel2,email1,email2,nomf,adressf,niff,nota FROM usuaris WHERE nom='$nom'";

$query=mysql_query($select);

if (!$query) {
    die('Invalid query: ' . mysql_error());
    }
    
list($nom,$tip,$tip2,$dia,$components,$tel1,$tel2,$email1,$email2,$nomf,$adressf,$niff,$nota)=mysql_fetch_row($query);

?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>visualitzar fitxa familiar ::: la coope</title>
	</head>
	
<body>

<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid orange;">

<p class='path'> 
><a href='admint.php'>administració</a>
>><a href='families.php'>llistat famílies</a> 
>>><a href='vis_user.php?id=<?php echo $nom; ?>'>veure família <?php echo $supernom; ?></a>
</p>
<p class="h1" style="background: orange; text-align: left; padding-left: 20px;">
Veure família <?php echo $nom; ?>
</p>

<div class="contenidor_fac" style=" width:500px; border: 1px solid orange; margin-bottom:20px;">

<table style="padding: 10px;" width="100%" align="center" cellspading="5" cellspacing="5" >
<tr class="cos_majus"><td>Nom:</td><td style="color:grey;"><?php echo $nom; ?></td></tr>
<tr class="cos_majus"><td>Dia de recollida:</td><td style="color:grey;"><?php echo $dia; ?></td></tr>
<tr class="cos_majus"><td>Components:</td><td style="color:grey;"><?php echo $components; ?></td></tr>
<tr class="cos_majus"><td>Telèfon principal:</td><td style="color:grey;"><?php echo $tel1; ?></td></tr>
<tr class="cos_majus"><td>Telèfon alternatiu:</td><td style="color:grey;"><?php echo $tel2; ?></td></tr>
<tr><td class="cos_majus">e-mail principal:</td><td class="cos" style="color:grey;"><?php echo $email1; ?></td></tr>
<tr><td class="cos_majus">e-mail alternatiu:</td><td class="cos" style="color:grey;"><?php echo $email2; ?></td></tr>
<tr><td class="cos_majus">Nom per la factura:</td><td class="cos" style="color:grey;"><?php echo $nomf; ?></td></tr>
<tr><td class="cos_majus">Adreça per la factura:</td><td class="cos" style="color:grey;"><?php echo $adressf; ?></td></tr>
<tr><td class="cos_majus">NIF per la factura:</td><td class="cos" style="color:grey;"><?php echo $niff; ?></td></tr>
<tr><td class="cos_majus">Comentaris:</td><td class="cos"style="color:grey;"><?php echo $nota; ?></td></tr>
<tr class="cos_majus"><td>Permisos:</td><td style="color:grey;"><?php echo $tip; ?></td></tr>
</table>

<?php
if ($nom==$user) {

?>

<p class="linia_button2" style="background: orange; text-align: center; vertical-align: middle;">
<input class="button2" type="button" value="EDITAR" onClick="javascript:window.location = 'editdadesp.php';">
<input class="button2" type="button" value="SORTIR" onClick="javascript:history.go(-1);">
</p>

<?php
}
?>

</div></div></div>
</body>
</html>

<?php
include 'config/disconect.php';
} 
else {
header("Location: index.php"); 
}
?>