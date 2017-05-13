<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

$pactiu=$_POST['actiu'];
$pgrup=$_POST['grup'];
$ptipus=$_POST['tipus'];

include 'config/configuracio.php';

?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>llistat families ::: la coope</title>

</head>
<body>

<div class="pagina" style="margin-top: 10px;">

<div class="contenidor_1" style="border: 1px solid orange;">

<p class='path'> 
><a href='admint.php'>administració</a> 
>><a href='families.php'>llistat famílies</a>
</p>
<p class="h1" style="background: orange; text-align: left; padding-left: 20px;">
Llistat famílies
</p>
 
<table width="80%" align="center" style="padding: 10px 0px;">
<form action="families.php" method="post" name="fam" id="fam">
<tr>
<td width="33%" align="center" class="form">Actiu/Baixa</td>
<td width="33%" align="center" class="form">Grup</td>
<td width="33%" align="center" class="form">Comissió</td>
</tr>

<tr>
<td align="center">
<SELECT name="actiu" id="actiu" size="1" maxlength="5" onChange="this.form.submit()">

<?php

if ($pactiu=='actiu') {$checked1='selected';$checked2="";}
if ($pactiu=='baixa') {$checked2='selected';$checked1="";}


?>
	<option value="">Tots</option>
	<option value="actiu" <?php echo $checked1; ?>>Actiu</option>
	<option value="baixa" <?php echo $checked2; ?>>Baixa</option>

</select>
</td>

<td align="center">
<SELECT name="grup" id="grup" size="1" maxlength="30" onChange="this.form.submit()">
<option value="">Tots</option>

<?php
$select3= "SELECT nom FROM grups ORDER BY nom";
$query3=mysql_query($select3);
if (!$query3) {die('Invalid query3: ' . mysql_error());}

while (list($sgrup)=mysql_fetch_row($query3)) 
{
	if ($pgrup==$sgrup){echo '<option value="'.$sgrup.'" selected>'.$sgrup.'</option>';}
	else {echo '<option value="'.$sgrup.'">'.$sgrup.'</option>';}
}

?>

</select>
</td>
<td align="center">
<SELECT name="tipus" id="tipus" size="1" maxlength="30" onChange="this.form.submit()">
<option value="">Tots</option>
<?php
if ($ptipus=='user') {$checked3='selected';$checked4="";$checked5="";$checked6="";$checked7="";$checked8="";}
elseif ($ptipus=='admin') {$checked3='';$checked4="selected";$checked5="";$checked6="";$checked7="";$checked8="";}
elseif ($ptipus=='eco') {$checked3='';$checked4="";$checked5="selected";$checked6="";$checked7="";$checked8="";}
elseif ($ptipus=='prov') {$checked3='';$checked4="";$checked5="";$checked6="selected";$checked7="";$checked8="";}
elseif ($ptipus=='cist') {$checked3='';$checked4="";$checked5="";$checked6="";$checked7="selected";$checked8="";}
elseif ($ptipus=='super') {$checked3='';$checked4="";$checked5="";$checked6="";$checked7="";$checked8="selected";}
echo'
<option value="user" '.$checked3.'>user</option>
<option value="admin" '.$checked4.'>admin</option>
<option value="eco" '.$checked5.'>eco</option>
<option value="prov" '.$checked6.'>prov</option>
<option value="cist" '.$checked7.'>cist</option>
<option value="super" '.$checked8.'>super</option>';
?>

</select>
</td>
</form>
</tr></table>

<div class="contenidor_fac" style="border: 1px solid orange; max-height: 350px; overflow: scroll; overflow-x: hidden; 
margin-bottom: 20px; padding-bottom: 20px;">

<?php

	if ($pactiu!="" AND $pgrup=="" AND $ptipus=="") {$where="WHERE tipus2='".$pactiu."'"; $title="Famílies en ".$pactiu;}
	elseif ($pactiu!="" AND $pgrup!="" AND $ptipus=="") {$where="WHERE tipus2='".$pactiu."' AND dia='".$pgrup."'"; $title="Famílies en ".$pactiu." del grup ".$pgrup;}
	elseif ($pactiu!="" AND $pgrup!="" AND $ptipus!="") {$where="WHERE tipus2='".$pactiu."' AND dia='".$pgrup."' AND tipus='".$ptipus."'"; $title="Famílies en ".$pactiu." del grup ".$pgrup." i de la comissió ".$ptipus;}
	elseif ($pactiu!="" AND $pgrup=="" AND $ptipus!="") {$where="WHERE tipus2='".$pactiu."' AND tipus='".$ptipus."'"; $title="Famílies en ".$pactiu." de la comissió ".$ptipus;}
	elseif ($pactiu=="" AND $pgrup=="" AND $ptipus!="") {$where="WHERE tipus='".$ptipus."'"; $title="Famílies de la comissió ".$ptipus;}
	elseif ($pactiu=="" AND $pgrup!="" AND $ptipus!="") {$where="WHERE dia='".$pgrup."' AND tipus='".$ptipus."'"; $title="Famílies del grup ".$pgrup." i de la comissió ".$ptipus;}
	elseif ($pactiu=="" AND $pgrup!="" AND $ptipus=="") {$where="WHERE dia='".$pgrup."'"; $title="Famílies del grup ".$pgrup;}
	elseif ($pactiu=="" AND $pgrup=="" AND $ptipus=="") {$where=""; $title="Totes les famílies";}
	
	print ('<p class="h1" 
		style="background: orange; font-size:14px; text-align: left; 
		height: 20px; padding-left: 20px;">
		'.$title.'
		</p>');
	
	print('<table width="100%" align="center" cellspading="5" cellspacing="5" >
		<tr class="cos_majus">
		<td align="center" width="20%">nom</td>
		<td align="center" width="30%">components</td>
		<td align="center" width="15%">telefon</td>
		<td align="center" width="35%">email</td> </tr>');

	$taula = "SELECT nom,components,tel1,email1 FROM usuaris ".$where." ORDER BY nom";
	$result = mysql_query($taula);
	if (!$result) {die('Invalid query: ' . mysql_error());}

	while (list($nom,$components,$tel1,$email1)=mysql_fetch_row($result))
	{
		echo "<tr class='cos'>
		<td align='center'><a href='vis_user.php?id=".$nom."'>".$nom." </a></td>
		<td align='center'>".$components."</td>
		<td align='center'>".$tel1."</td>
		<td align='center'>".$email1."</td>
		</tr>";
	}

	echo "</table></div></div>";

?>

<p class="cos2" style="clear: both; text-align: center;">
Per veure la fitxa completa d'una família clicka sobre el seu nom
</p>	
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