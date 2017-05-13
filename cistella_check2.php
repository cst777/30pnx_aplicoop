<?php

session_start();

if ($_SESSION['image_is_logged_in']) 
{

$user = $_SESSION['user'];
$_SESSION['codi_cistella']='off';

$gproces=$_GET['id'];
$ggrup=$_GET['id2'];
$gbd_data=$_GET['id3'];

$gdata=date('d-m-Y',strtotime('$gbd_data'));

?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />			
		<title>citelles - generacio factures ::: la coope</title>
</head>

<body>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid green;">
<p class='path'> 
><a href='admint.php'>administració</a> 
>><a href='grups_comandes.php'>grups de comandes i cistelles</a>
>>><a href='cistella_check2.php?id=<?php echo $gproces.'&id2='.$ggrup.'&id3='.$gbd_data; ?>'>factures generades proces <?php echo $gproces.' - '.$ggrup.' - '.$gbd_data; ?></a>
</p>
<p class="h1" style="background: green; text-align: left; padding-left: 20px;">
Generació factures
</p>

<p class='error' style='font-size: 14px;'>
Les factures de cada família s'han generat correctament. Podeu veure-les o imprimir-les clicant a sobre.
</p>

<div class="contenidor_fac" style="border: 1px solid green;">
<table width="80%" align="center" valign="middle" cellpadding="5" cellspacing="5">

<?php

echo "<tr class='cos_majus'><td width='55%' align='left'>Família (número comanda)</td>";
echo "<td width='15%' align='center'>productes demanats</td>";
echo "<td width='15%' align='center'>productes servits</td>";
echo "<td width='15%' align='center'>total a pagar</td>";
echo "</tr>";

include 'config/configuracio.php';

$taula = "SELECT numero, usuari, check0
FROM comanda
WHERE proces='$gproces' AND grup='$ggrup' AND data='$gbd_data'
ORDER BY numero";

$result = mysql_query($taula);
if (!$result) {die('Invalid query: ' . mysql_error());}

while (list($numero,$familia,$check0)=mysql_fetch_row($result))
{
	$taula2 = "SELECT SUM(quantitat), SUM(cistella), SUM(cistella*preu*(1-descompte)*(1+iva))
	FROM comanda_linia
	WHERE numero='$numero' 
	GROUP BY numero";

	$result2 = mysql_query($taula2);
	if (!$result2) {die('Invalid query2: ' . mysql_error());}

	list($totcom,$totcist,$totpreu)=mysql_fetch_row($result2);
	$totcom=sprintf("%01.2f",$totcom);
	$totcist=sprintf("%01.2f",$totcist);
	$totpreu=sprintf("%01.2f",$totpreu);
	
?>

<tr class='cos'>
<td align="left"><a href='factura.php?id=<?php echo $numero; ?>'>
<?php echo $familia; ?> (<?php echo $numero; ?>)</a></td>

<?php
	echo "<td align='center'>".$totcom."</td>";
	echo "<td align='center'>".$totcist."</td>";
	echo "<td align='center'>".$totpreu."€</td>";
	echo "</tr>";	
}
?>

</table>
</div>

<p class="linia_button2" style="background: green; text-align: center; vertical-align: middle;">
<input class="button2" name="sortir" type="button" value="FINALITZAR" onClick="javascript:window.location = 'admint.php';">
<input class="button2" style="width: 90px;" name="sortir" type="button" value="INCIDÈNCIES" 
	onClick="javascript:window.location = 'cistella_incidencia.php?id=<?php echo $gproces.'&id2='.$ggrup.'&id3='.$gbd_data; ?>';">
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