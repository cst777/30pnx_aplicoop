<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) 
{
	$user = $_SESSION['user'];
	$_SESSION['codi_cistella']='off';

	$gcont=$_GET['id'];
	$gdata=$_GET['id2'];
	$gproces=$_GET['id3'];
	$ggrup=$_GET['id4'];

	list($gdia, $gmes, $gany) = explode("-", $gdata);
	$gbd_data=$gany.'-'.$gmes.'-'.$gdia;
	
function tradueixData2($d)
{ 
				$angles = array(    "Monday",
                                "Tuesday",
                                "Wednesday",
                                "Thursday",
                                "Friday",
                                "Saturday",
                                "Sunday",
                                "Mon",
                                "Tue",
                                "Wed",
                                "Thu",
                                "Fri",
                                "Sat",
                                "Sun",
                                "January",
                                "February",
                                "March",
                                "April",
                                "May",
                                "June",
                                "July",
                                "August",
                                "September",
                                "October",
                                "November",
                                "December",
                                "Jan",
                                "Feb",
                                "Mar",
                                "Apr",
                                "May",
                                "Jun",
                                "Jul",
                                "Aug",
                                "Sep",
                                "Oct",
                                "Nov",
                                "Dec");

 				$catala = array(    "/Dilluns/",
                                "/Dimarts/",
                                "/Dimecres/",
                                "/Dijous/",
                                "/Divendres/",
                                "/Dissabte/",
                                "/Diumenge/",
                                "/Dll/",
                                "/Dmr/",
                                "/Dmc/",
                                "/Djs/",
                                "/Dvd/",
                                "/Dss/",
                                "/Dmg/",
                                "/Gener/",
                                "/Febrer/",
                                "/Març/",
                                "/Abril/",
                                "/Maig/",
                                "/Juny/",
                                "/Juliol/",
                                "/Agost/",
                                "/Setembre/",
                                "/Octubre/",
                                "/Novembre/",
                                "/Desembre/",
                                "/Gen/",
                                "/Feb/",
                                "/Mar/",
                                "/Abr/",
                                "/Mai/",
                                "/Jun/",
                                "/Jul/",
                                "/Ago/",
                                "/Set/",
                                "/Oct/",
                                "/Nov/",
                                "/Des/");

		$ret2 = preg_replace($catala, $angles, $d);
		return $ret2; 
}

	include 'config/configuracio.php';
	
	$errorMessage = '';
	if (isset($_POST['txtcodi']) && isset($_POST['data'])) 
	{
	   $pcodi = $_POST['txtcodi'];
	   $pproces = $_POST['proces'];
	   $pgrup = $_POST['grup'];
	   $pbd_data = $_POST['data'];
	   
		list($pany, $pmes, $pdia) = explode("-", $pbd_data);
		$pdata=$pdia.'-'.$pmes.'-'.$pany;
		
		if ($pcodi=="")
		{
			$errorMessage = "Has d'introduir una clau";
		}
		else
		{
			$sql = "SELECT codi FROM cistella_check 
			WHERE codi = '$pcodi' 
			AND proces='$pproces' AND grup='$pgrup' AND data ='$pbd_data'";
  	   	$result = mysql_query($sql) or die('Query failed. ' . mysql_error());
    		if (mysql_num_rows($result) == 1) 
     		{
/*   30 Panxes
  Al re-obrir una cistella tancada marquem el check1 = 0
  Quan la tanquin fent "PAS SEGÜENT" es canviarà check1 = 1
  D'aquesta manera sabem quines cistelles no s'han tancat correctament
  després de reobrir-les (la qual cosa pot produir incongruencies entre
  els valors del moneder i les facturacions per familia)
*/
			$sql = "UPDATE cistella_check 
				SET check1='0', codi='$pcodi' 
				WHERE  proces='$pproces' AND grup='$pgrup' AND data='$pbd_data'";
				$result = mysql_query($sql);
				if (!$result) {die('Invalid query- Upd cistella_check: ' . mysql_error());}
// 	END - 30 Panxes
     		$_SESSION['codi_cistella']='in';
 	      	?>
 	      
				<SCRIPT LANGUAGE="javascript">
				<!--
				window.location='cistelles.php?id2=<?php echo $pdata."&id3=".$pproces."&id4=".$pgrup."&id5=1"; ?>';
				-->
				</SCRIPT>
			         
	 	   	<?php
	      	exit;
      	} 
			else 
      	{
	   	   $errorMessage = "Ho sentim, la clau d'edició no és correcta. Prova altra vegada.";
   		   include 'config/disconect.php';
      	}  
		}
		$nota="";
		if ($errorMessage != '') 
		{
			$nota="<p class='error'>".$errorMessage."</p>";
		}
	}

?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>grups de comandes i cistelles::: la coope</title>
</head>

<body>
<div class="pagina" style="margin-top: 10px;">

<div class="contenidor_1" style="border: 1px solid green;">
<p class='path'> 
><a href='admint.php'>administració</a> 
>><a href='grups_comandes.php'>grups de comandes i cistelles</a>
</p>
<p class="h1" style="background: green; text-align: left; padding-left: 20px;">Grups de comandes i cistelles</p>

<?php echo $nota; ?>
<table width="80%" cellpadding="5" cellspacing="5" align="center" valign="middle">
<form action="" method="post" name="frmLogin" id="frmLogin">
<?php
	include 'config/configuracio.php';

////////////////////////////////////////////////////////
// si s'envia una data concreta mira si te el check1 //
///////////////////////////////////////////////////////
	if ($gdata!="")
	{
		$sql = "SELECT check1 FROM cistella_check 
		WHERE proces='$gproces' AND grup='$ggrup' AND data ='$gbd_data'";
   	$result = mysql_query($sql) or die('Query failed. ' . mysql_error());
  		list($check)=mysql_fetch_row($result);

//////////////////////////////////////////////////////////////////////////////////
// si el proces te check1=1 (ja s'han fet les cistelles) llavors demana el codi //
//////////////////////////////////////////////////////////////////////////////////
  		
  		if ($check=="1")
  		{
 	    	echo '<tr class="cos_majus" align="center">';
 	    	echo "<td>Proces - Grup</td>";
			echo "<td>Data</td>";
			echo "<td>Codi d'edició</td>";
			echo "</tr>";
?>

<tr>
<td align="center" class='cos'><?php echo $gproces."-".$ggrup; ?></td>
<input type=hidden name="proces" id="proces" value="<?php echo $gproces; ?>">
<input type=hidden name="grup" id="grup" value="<?php echo $ggrup; ?>">
<td align="center" class='cos'><?php echo $gdata; ?></td>
<input type=hidden name="data" id="data" value="<?php echo $gbd_data; ?>">
<td align="center"><input name="txtcodi" type="text" maxlength="7" size="5" id="txtcodi" value=""></td>
</tr></table>

<p class="linia_button2" style="background: green; text-align: center; vertical-align: middle;">
<input class="button2" name="submit" type="submit" value="ACCEPTAR">
<input class="button2" name="sortir" type="button" value="SORTIR" onClick="javascript:history.go(-1);">
</p>

<?php
     	}
     	
/////////////////////////////////////////////////////////////////
// si el proces te check1=0 passa directament a fer la cistella //
/////////////////////////////////////////////////////////////////
     	else
     	{
     		$_SESSION['codi_cistella']='in';
     		echo '<META HTTP-EQUIV="Refresh" Content="0; 
     		URL=cistelles.php?id2='.$gdata.'&id3='.$gproces.'&id4='.$ggrup.'&id5=1">';    
     		exit;
		}
	}
	
	
///////////////////////////////////////////////////////////////////////////////////
// si no s'envia una data concreta fa el llistat normal de processos i cistelles //
///////////////////////////////////////////////////////////////////////////////////
	else
	{
		echo "<tr class='cos_majus' align='center'><td>Proces - Grup</td>";
		echo "<td>Data</td>";
		echo "<td>Comanda proveïdores <br/><span class='cos'>sense l'estoc</span></td>";
		echo "<td>Veure totals comanda <br/><span class='cos'>inclou l'estoc</span></td>";
		echo "<td>Veure cistelles</td>";
		echo "<td>Editar cistella</td>";
		echo "<td>Pagament proveïdores</td>";
		echo "</tr>";

		$taula = "SELECT proces, grup, data
		FROM comanda
		GROUP BY proces, grup, data
		ORDER BY data DESC";
		$result = mysql_query($taula);
		if (!$result) {die('Invalid query: ' . mysql_error());}
		$rnum=mysql_num_rows($result);

		if (!$gcont) {$cont=20;}
		else {$cont=$gcont;}

		$taula2 = "SELECT proces, grup, data
		FROM comanda
		GROUP BY proces, grup, data
		ORDER BY data DESC
		LIMIT ".$cont;
		$result2 = mysql_query($taula2);
		if (!$result2) {die('Invalid query2: ' . mysql_error());}

		while (list($proces,$grup,$bd_data)=mysql_fetch_row($result2))
		{
			//$eco_column="";
			//if ($user=="economia")
			//{
			//	$taula2 = "SELECT codi FROM cistella_check WHERE data='$periode'";
			//	$result2 = mysql_query($taula2);
			//	if (!$result2) {die('Invalid query2: ' . mysql_error());}
			//	list($eco_codi)=mysql_fetch_row($result2);
			//	$eco_column="<td align='center' class='Estilo1'>".$eco_codi."</td>";
			//	}
			
			$data=date("d-m-Y", strtotime ($bd_data));
?>

<tr>
<td align="center" class='cos'><?php echo $proces."-".$grup; ?></td>
<td align="center" class='cos'><?php echo $data; ?></td>
<td align="center" class='cos'><a href='totalcomanda.php?id=<?php echo $data."&id2=".$proces."&id3=".$grup; ?>&id4=0'>CP</a></td>
<td align="center" class='cos'><a href='totalcomanda.php?id=<?php echo $data."&id2=".$proces."&id3=".$grup; ?>&id4=1'>VT</a></td>

<?php
			$taula3 = "SELECT check1
			FROM cistella_check
			WHERE proces='$proces' AND grup='$grup' AND data='$bd_data'";
			$result3 = mysql_query($taula3);
			if (!$result3) {die('Invalid query3: ' . mysql_error());}
			
			list($check)=mysql_fetch_row($result3);
			if ($check==1)
			{
				$vis_cist="<a href='cistelles.php?id2=".$data."&id3=".$proces."&id4=".$grup."&id5=0'>VC</a>";
			}
			else
			{
				$vis_cist="";
			}
			
?>
<td align="center" class='cos'><?php echo $vis_cist; ?></td>
<td align="center" class='cos'><a href='grups_comandes.php?id2=<?php echo $data."&id3=".$proces."&id4=".$grup; ?>'>E</a></td>
<td align="center" class='cos'><a href='totalfactura.php?id=<?php echo $data."&id2=".$proces."&id3=".$grup; ?>'>P</a></td>
</tr>

<?php
			$i++;
		}
		echo '</table>';

		if ($rnum>$cont)
		{
			$id=$cont+20;
?>

<p class="linia_button2">
<input name="mes" type="button" value="+20" onClick="javascript:window.location = 'grups_comandes.php?id=<?php echo $id; ?>';">
</p>

<?php
		}
?>

</div>
</div>
</body>
</html>

<?php
include 'config/disconect.php';
	}
} 
else 
{
	header("Location: index.php"); 
}
?>