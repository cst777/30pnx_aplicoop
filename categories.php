<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

$gtipus = $_GET['id'];
$gactiu = $_GET['id2'];
$gestoc = $_GET['id4'];
$elim = $_GET['id3'];

include 'config/configuracio.php';

?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />	
		<title>editar categories ::: la coope</title>
		
<body>
<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid #990000;">
<p class='path'> 
><a href='admint.php'>administració</a> 
>><a href='categories.php'>crear, editar i eliminar categories</a> 
</p>
<p class="h1" style="background: #990000; text-align: left; padding-left: 20px;">
Crear, editar i eliminar categories
<span style="display: inline; float: right; text-align: center; vertical-align: middle; 
padding: 2px 50px 2px 0px;">
<input class="button2" style="width:180px;" type="button" value="CREAR NOVA CATEGORIA" onClick="javascript:window.location = 'createcat.php';">
</span>
</p>


<?php
if ($gestoc != "")
			{
				$query4 = "UPDATE categoria
				SET estoc='".$gestoc."'
				WHERE tipus='".$gtipus."' ";
				mysql_query($query4) or die('Error, insert query4 failed');			
			}

if ($gactiu != "")
			{
				$query3 = "UPDATE categoria
				SET actiu='".$gactiu."'
				WHERE tipus='".$gtipus."' ";
				mysql_query($query3) or die('Error, insert query3 failed');			
			}
						
if ($elim != "")
			{
				$select= "SELECT subcategoria FROM subcategoria	
				WHERE categoria='".$gtipus."' ";
				$result = mysql_query($select) or die("Query failed. " . mysql_error());
   	
   			if (mysql_num_rows($result) >= 1)
   			{
   				die
   				("<p class='comment'>La categoria ".$gtipus." posseeix subcategories.</p>
   				<p class='comment'>Hauries de borrar-les en primer terme.</p>"); 
 		  		}
   			else
   			{
				$query4 = "DELETE FROM categoria
				WHERE tipus='".$gtipus."' ";
				mysql_query($query4) or die('Error, insert query4 failed');	
				
				echo "<p class='comment'>La categoria ".$gtipus." s'ha eliminat correctament</p>";
				}		
			}

?>

<div class="contenidor_fac" style="border: 1px solid #990000; margin-bottom: 20px;" >
<table width="80%" align="center" valign="middle" cellpadding="5" cellspacing="5">
<tr class="cos_majus">
<td width="20%" align="center">CATEGORIA</td><td width="20%" align="center">ACTIVAT</td>
<td width="20%" align="center">ESTOC</td>
<td width="20%" align="center">SUBCATEGORIES</td><td width="20%" align="center">ELIMINAR</td>
</tr>
<?php

$taula = "SELECT tipus, actiu, estoc FROM categoria 
		ORDER BY actiu, tipus";
$result = mysql_query($taula);
if (!$result) {die('Invalid query: ' . mysql_error());}

$k=0;
while (list($tipus,$actiu,$estoc)=mysql_fetch_row($result))
{
$checked1="";
$checked2="";
if ($actiu=="activat") {$checked1="checked";}
else {$checked2="checked";}
$checked3="";
$checked4="";
if ($estoc=="si") {$checked3="checked";}
else {$checked4="checked";}

?>


<tr class="cos">
<td align="center">
<input type="text" name="tipus" id="tipus" value="<?php echo $tipus; ?>" size="15" maxlength="30" readonly></td>
</td>
<td align="center">
si<input type="radio" name="actiu<?php echo $k; ?>" value="activat" id="actiu<?php echo $k; ?>" <?php echo $checked1; ?> onClick="javascript:window.location = 'categories.php?id=<?php echo $tipus; ?>&id2=activat';">
no<input type="radio" name="actiu<?php echo $k; ?>" value="desactivat" id="actiu<?php echo $k; ?>" <?php echo $checked2; ?> onClick="javascript:window.location = 'categories.php?id=<?php echo $tipus; ?>&id2=desactivat';">
</td>
<td align="center">
si<input type="radio" name="estoc<?php echo $k; ?>" value="si" id="estoc<?php echo $k; ?>" <?php echo $checked3; ?> onClick="javascript:window.location = 'categories.php?id=<?php echo $tipus; ?>&id4=si';">
no<input type="radio" name="estoc<?php echo $k; ?>" value="no" id="estoc<?php echo $k; ?>" <?php echo $checked4; ?> onClick="javascript:window.location = 'categories.php?id=<?php echo $tipus; ?>&id4=no';">
</td>
<td align="center"><a href='subcategories.php?id=<?php echo $tipus; ?>'>S</a></td>
<td align="center">
<a href='categories.php?id=<?php echo $tipus; ?>&id3=borrar' 
onClick='if(confirm("Estas segur que vols eliminar aquesta categoria <?php echo $tipus; ?>?") == false){return false;}'>X</a>
</td>

<?php
$k++;
}
echo "</tr></table></div>";

?>


<p class="cos2" style="clear: both; text-align: center; padding: 0px 100px;">
Per activar o desactivar o per permetre que una categoria tingui estoc o no clica el botó desitjat.
Per editar subcategories clica la S en la categoria que et convingui. 
Per borrar clica sobre la X de la columna ELIMINAR</p>

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