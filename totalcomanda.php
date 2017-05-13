<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

$data=$_GET['id'];
$proces=$_GET['id2'];
$grup=$_GET['id3'];
$estoc=$_GET['id4'];

$bd_data=date("Y-m-d", strtotime ($data));

if ($estoc==1)
{
$link="&id4=1";
$title="amb estoc";
$where="";
}
else
{
$link="&id4=0";
$title="sense estoc";
$where="AND cat.estoc = 'no' ";
}

?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>total comanda amb o sense estoc ::: la coope</title>
		<style type="text/css">
	a#color:link, a#color:visited {color:white; border: 1px solid #9cff00;}
	a#color:hover {color:black; border: 1px solid #9cff00;   -moz-border-radius: 10%;}
   a#color:active {color:white; border: 1px solid #9cff00;  -moz-border-radius: 10%;}
		</style>
</head>

<body>

<div class="pagina" style="margin-top: 10px;">

<div class="contenidor_1" style="border: 1px solid green;">
<p class='path'> 
><a href='admint.php'>administració</a> 
>><a href='grups_comandes.php'>grups de comandes i cistelles</a> 
>>><a href='totalcomanda.php?id=<?php echo $data."&id2=".$proces."&id3=".$grup.$link; ?>'>comanda total <?php echo $title." ".$proces."-".$grup."-".$data; ?></a>
</p>
<p class="h1" style="background: green; text-align: left; padding-left: 20px;">Comanda total <?php echo $title." ".$proces."-".$grup."-".$data; ?></p>
<div class="cat" style="width: 750px; margin-left: auto; margin-right: auto;">


<?php

include 'config/configuracio.php';
$color=array("#F0C900","#00b2ff","orange","#b20000","#14e500","red","#8524ba");
$cc=0;
$select3="SELECT nom FROM proveidores";
$resultat3=mysql_query($select3);
if (!$resultat3) {die("Query to show fields from table select3 failed");}
$numrowsat3=mysql_numrows($resultat3);
while (list($prove)=mysql_fetch_row($resultat3))
{
	$select2="SELECT cl.numero, c.data, cl.ref, pr.nom, pr.proveidora, pr.categoria, cat.estoc
	FROM comanda_linia AS cl, comanda AS c, productes AS pr, categoria AS cat
	WHERE c.numero=cl.numero AND cl.ref=pr.ref AND pr.proveidora='$prove' 
	AND c.proces='$proces' AND pr.categoria=cat.tipus AND c.grup='$grup' 
	AND c.data='$bd_data' ".$where."
	ORDER BY pr.proveidora, pr.nom";
	$resultat2=mysql_query($select2);
	if (!$resultat2) {die("Query to show fields from table select2 failed");}
	$numrowsat2=mysql_numrows($resultat2);
	
	if ($numrowsat2!=0)
	{
		print ('<a href="#'.$prove.'" id="color" style="background: '.$color[$cc].'; 
				margin-bottom: 5px; margin-right: 3px; white-space: -moz-pre-wrap; word-wrap: break-word;">
				<span>'.$prove.'</span></a>');
				$cc++;
				if ($cc==7){$cc=0;}
	}
	mysql_free_result($resultat2);
}


echo'</div>	
<div class="contenidor_fac" style="border: 1px solid green; height: 350px; overflow: scroll;">';

$cc=0;
$select3="SELECT nom FROM proveidores";
$resultat3=mysql_query($select3);
if (!$resultat3) {die("Query to show fields from table select3 failed");}
$numrowsat3=mysql_numrows($resultat3);
while (list($prove)=mysql_fetch_row($resultat3))
{
	$select2="SELECT cl.numero, c.data, cl.ref, pr.nom, pr.proveidora, pr.categoria, cat.estoc
	FROM comanda_linia AS cl, comanda AS c, productes AS pr, categoria AS cat
	WHERE c.numero=cl.numero AND cl.ref=pr.ref AND c.proces='$proces' AND pr.categoria=cat.tipus
	AND c.grup='$grup' AND c.data='$bd_data' AND pr.proveidora='$prove' ".$where."
	ORDER BY pr.proveidora, pr.nom";
	$resultat2=mysql_query($select2);
	if (!$resultat2) {die("Query to show fields from table select2 failed");}
	$numrowsat2=mysql_numrows($resultat2);
	
	if ($numrowsat2!=0)
	{
		echo '<a name="'.$prove.'"></a> 
		<p class="h1"
		style="background: '.$color[$cc].'; font-size:14px; text-align: left; 
		height: 20px; padding-left: 20px; clear: left;">
		'.$prove.'
		</p>';
		$cc++;
		if ($cc==7){$cc=0;}
		echo '<table width="100%" align="center" valign="middle" cellpadding="5" cellspacing="5">';
	
		$query= "SELECT numero,usuari FROM comanda 
		WHERE proces='$proces' AND grup='$grup' AND data='$bd_data' 
		ORDER BY usuari";
		$result=mysql_query($query);
		if (!$result) { die("Query to show fields from table failed");}
		$numrows1=mysql_numrows($result);
		
		echo "<tr class='cos_majus'><td>Producte</td>";
		echo "<td>Totals</td>";

		// printing table headers
		$i=0;
		while (list($numero,$familia)=mysql_fetch_row($result))
		{
			$fila[]=$numero;
			echo "<td>".$familia." (".$fila[$i].")</td>";
			$i++;
		}
		echo "</tr>";

/* Modified by 30 Panxes - December 2016
		$taula = "SELECT cl.ref, pr.nom, pr.unitat, SUM(cl.quantitat) AS sum 
		FROM comanda AS c, comanda_linia AS cl, productes AS pr
		WHERE c.numero=cl.numero AND pr.ref=cl.ref AND c.proces='$proces' 
		AND c.grup='$grup' AND c.data='$bd_data' AND pr.proveidora='$prove'
		GROUP BY cl.ref
		ORDER BY pr.nom";
*/

		$taula = "SELECT cl.ref, pr.nom, pr.unitat, SUM(cl.quantitat) AS sum 
		FROM comanda AS c, comanda_linia AS cl, productes AS pr, categoria AS cat
		WHERE c.numero=cl.numero AND pr.ref=cl.ref AND c.proces='$proces' 
		AND c.grup='$grup' AND c.data='$bd_data' AND pr.proveidora='$prove' AND pr.categoria=cat.tipus ".$where."
		GROUP BY cl.ref
		ORDER BY pr.nom";

		$result = mysql_query($taula);
		if (!$result) {die('Invalid query taula: ' . mysql_error());}
		while (list($ref,$nomprod,$uni,$sum)=mysql_fetch_row($result))
		{

?>

<tr class='cos'><td><?php echo $nomprod; ?></td>
<td><?php echo $sum; ?> <?php echo $uni; ?></td>

<?php

			$taula2 = "SELECT c.numero, c.usuari, cl.ref, cl.quantitat 
			FROM comanda AS c
			LEFT JOIN comanda_linia AS cl ON c.numero=cl.numero
			WHERE c.proces='$proces' AND c.grup='$grup' AND c.data='$bd_data' 
			AND cl.ref='$ref'
			ORDER BY c.usuari";
			$result2 = mysql_query($taula2);
			if (!$result2) {die('Invalid query: ' . mysql_error());}

			$j=0;
			while(list($numcmda,$familia,$nomprod2,$quant)=mysql_fetch_row($result2))
			{
				$numrows2=mysql_numrows($result2);

				for ($i=$j; $i<=$numrows1; $i++) 
				{
					$numfila=$fila[$i];
 					if ($numcmda==$numfila)
 					{
						echo "<td>".$quant."</td>";
 						$j++;
 						break;
 					}
 					else
 					{
 					echo"<td>&nbsp</td>";
 					$j++;
 					}
				}
			}
			echo "</tr>";
		}
	}
			echo "</table>";
}
?>
</div>

<p class="linia_button2" style="padding:4px 0px; height: 20px; background: green; text-align: center; vertical-align: middle;">
<input class="button2" type="button" value="CREAR ARXIU CSV" style="width: 120px;"
onClick="javascript:window.location = 'createcsv2.php?id=<?php echo $bd_data."&id2=".$proces."&id3=".$grup.$link; ?>'">
</input>
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