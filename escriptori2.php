<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

setlocale(LC_ALL,"CA");
date_default_timezone_set("Europe/Madrid"); 

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

// processos oberts//
	$sel = "SELECT dia FROM usuaris	WHERE nom='$user'";
	$result = mysql_query($sel);
	if (!$result) { die('Invalid query: ' . mysql_error());}
	list ($grup) = mysql_fetch_row($result);

	$sel2 = "SELECT nom, tipus, data_inici, data_fi, periode, dia_recollida, dia_tall, hora_tall
	 FROM processos WHERE grup='$grup' AND actiu='actiu'";
	$result2 = mysql_query($sel2);
	if (!$result2) { die('Invalid query2: ' . mysql_error());}	
	
	while (list($proces,$tipus,$datai,$dataf,$periode,$diare,$diat,$horat) = mysql_fetch_row($result2))
	{
		$time_avui = time();
		list($yi, $mi, $di) = explode('-', $datai);
		$time_datai= mktime(0, 0, 0, $mi, $di, $yi);
		list($yf, $mf, $df) = explode('-', $dataf);
		$time_dataf= mktime(23, 59, 59, $mf, $df, $yf);
		
		if ($tipus=="període concret" AND $time_avui<=$time_dataf AND $time_avui>=$time_datai)
		{
			$ver_datai = date("d-m-Y", $time_datai);
			$ver_dataf = date("d-m-Y", $time_dataf);
			$bd_dataf = date("Y-m-d", $time_dataf);
			$sel3 = "SELECT numero	FROM comanda 
			WHERE usuari='$user' AND proces='$proces' AND data<='$dataf' AND data>='$datai' ";
			$result3 = mysql_query($sel3);
			if (!$result3) {die('Invalid query3: ' . mysql_error());}
			list ($numcmda1) = mysql_fetch_row($result3);
			
			if ($numcmda1!="") 
			{
				$nota11=$proces.': tens la comanda numero '.$numcmda1.'
				<a href="cmda2.php?id='.$proces.'&id2='.$numcmda1.'&id4=vis" target="cos" 
				title="clica per editar aquesta comanda">Edita-la</a> fins el dia '.$ver_dataf.' (inclòs).';
			}
			else 
			{
				$nota11=$proces.': fins '.$ver_dataf.' (inclòs). 
				<a href="cmda2.php?id='.$proces.'&id4=create" target="cos" 
				title="clica per realitzar una nova comanda">Fer-la nova</a>';
			}
			$nota1 .=' <p class="cos_majus" style="margin: 5px 10px 0px 10px;">'.$nota11.'</p> ';
		}
		
		if ($tipus=="continu" AND $periode=="setmanal")
		{
			///Treiem l'hora i els minuts de l'hora de tall///
			$horat_0=(int)substr($horat,0,2);
			$mint_0=(int)substr($horat,2,2);
			///Fem l'operació per convertir lhora i minuta de tall en minuts///
			$horat_calc=(60*$horat_0)+$mint_0;
			$horat_verb=$horat_calc." minutes";
			/// Traduim el dia de la setmana de tall a l'angles i agafem le stres primeres lletres///
			$diare_a=tradueixData2(ucfirst($diare));
			$diat_a=tradueixData2(ucfirst($diat));
			$diat_w3=substr($diat_a, 0, 3);
			///Si el dia de la setmana d'avui coincideix amb el dia de al setmana de tall///
			///llavors mirem si falten hores i minuts per arribar al punt de tall///
			$diaw3_today=date('D');
			if ($diat_w3==$diaw3_today)
			{ 
				$hora_ara=(int)date('G');
				$min_ara=(int)date('i');
				$ara=($hora_ara*60)+$min_ara;
				/// Si encara no ha és l'hora i minut del punt de tall ///
				/// llavors avui és la data de tall ///
				if ($horat_calc>=$ara)
				{
					$diat_0=mktime(0,0,0,date('m'),date('d'),date('y'));
				}
				/// altres possibilitats la data de tall és ///
				/// el proper dia de la setmana de tall///
				else
				{
					$diat_0=strtotime("next ".$diat_a);
				}
			}
			else 
			{
				$diat_0=strtotime("next ".$diat_a);
			}
			/// data exacta de tall és la data de tall més les hores i minuts de tall///
			/// tall superior ///
			$time_diats=strtotime("+ ".$horat_verb, $diat_0);
			$ver_diats=date("d-m-Y H:i", $time_diats);
			/// tall inferior ///
			$diat_2=strtotime("- 7 days", $time_diats);
			$time_diati=strtotime("+ 1 second", $diat_2);
			$ver_diati=date("d-m-Y H:i", $time_diati);
			/// data de recollida és el següent dia de la setmana de recollida de la data de tall superior//
			$time_diare=strtotime("next ".$diare_a, $diat_0);
			$bd_diare=date("Y-m-d", $time_diare);
			$ver_diare=date("d-m-Y", $time_diare);

			$sel3 = "SELECT numero
			FROM comanda 
			WHERE usuari='$user' AND proces='$proces' AND data='$bd_diare'";
			$result3 = mysql_query($sel3);
			if (!$result3) {die('Invalid query3: ' . mysql_error());}
			list ($numcmda1) = mysql_fetch_row($result3);

			if ($numcmda1!="") 
			{
				$nota11=$proces.': tens la comanda numero '.$numcmda1.' per recollir el '.$ver_diare.'.
				<a href="cmda2.php?id='.$proces.'&id2='.$numcmda1.'&id4=vis" target="cos" 
				title="clica per editar aquesta comanda">Edita-la</a> fins '.$ver_diats;
			}
			else 
			{
				$nota11=$proces.': fins '.$ver_diats.'
				<a href="cmda2.php?id='.$proces.'&id4=create" target="cos" 
				title="clica per realitzar una nova comanda">Fer-la nova</a>';				
			}
			$nota1 .=' <p class="cos_majus" style="margin: 5px 10px 0px 10px;">'.$nota11.'</p>';
		}
	}
//

// Actualització check1 despres 1 mes cistelles //
	$sel4 = "SELECT numero, data2	FROM comanda 
			WHERE usuari='$user' AND check0='1' AND check1='0' AND data2!='0000-00-00'";
			$result4 = mysql_query($sel4);
	if (!$result4) {die('Invalid query4: ' . mysql_error());}
	while(list ($numero, $data2) = mysql_fetch_row($result4))
	{
		list($year, $month, $day) = explode('-', $data2);
		$data2v= mktime(0, 0, 0, $month, $day, $year);
		$data_cl=strtotime('+ 1 month', $data2v);
		$ara= time();
		if ($ara > $data_cl)
		{
			$sel6 = "UPDATE comanda SET check1='1'	WHERE numero='$numero'";
			$result6 = mysql_query($sel6) or die('Invalid query6: ' . mysql_error());
			$nota12 .= '<p class="cos_majus" style="color: grey; margin: 5px 10px 0px 10px;">
			La comanda numero '.$numero.' s\'ha validat automàticament.</p>';
		}
	}
//

	//notes a l'escriptori
	$datacomp= date ("Y-m-d");
	$sel2 = "SELECT * FROM notescrip WHERE caducitat>='$datacomp'";
	$result2 = mysql_query($sel2);
	if (!$result2) {
	    die('Invalid query result2: ' . mysql_error());
	}

	$nota13="";	$nota21="";
	while (list($num,$nom,$text,$tipus,$caduc)=mysql_fetch_row($result2)){
	list($any, $mes, $dia) = explode("-", $caduc);
	$caduc2=$dia."-".$mes.'-'.$any;

	if ($tipus=='esquerra'){
	$nota13 .='<p class="cos_majus" style="color: grey; margin: 5px 10px 0px 10px;">'.$text.'<SPAN style="font-size: 10px;"> ---> fins '.$caduc2.'</SPAN></p>';}
	else{
	$nota21 .='<p class="cos_majus" style="color: grey; margin: 5px 10px 0px 10px;">'.$text.'<SPAN style="font-size: 10px;"> ---> fins '.$caduc2.'</SPAN></p>';}
	}
	//
	
	// moneder //
	$sel6 = "SELECT SUM(valor) AS total FROM moneder WHERE familia='$user'"; //calcula realmente el total del moneder
	$result6 = mysql_query($sel6);
	if (!$result6) { die('Invalid query6: ' . mysql_error()); }
	list($moneder) = mysql_fetch_row($result6);
	$style="color: black;";
/* 30 Panxes */
	if (empty($moneder)) {$moneder = 0;}
/* end 30 Panxes */	
	if ($moneder <= 0) {$style='style="text-decoration: blink; color: red;"';}
	
	//darrers moviments //
	$sel7 = "SELECT data, concepte, valor FROM moneder WHERE familia='$user' ORDER BY data DESC LIMIT 5";
	$result7 = mysql_query($sel7);
	if (!$result7) { die('Invalid query7: ' . mysql_error()); }
	while(list($datam,$concepte,$valor) = mysql_fetch_row($result7))
	{
		$datam2=explode('-', $datam);
		$datamov=$datam2[2].'-'.$datam2[1].'-'.$datam2[0];
		if ($valor>0){$colin="style='color: blue;'";}
		else {$colin="style='color: red;'";}
		$last .="<tr><td align='center' width='25%'>".$datamov."</td>
		         <td align='left' width='60%'>".$concepte."</td>
		         <td align='right' width='15%' ".$colin.">".$valor."</td></tr>";
	}
	//
	
	// correus //
	
	$sel5 = "SELECT * FROM incidencia 
	WHERE vist='0' AND (`to`='$user' OR `from`='$user')
	ORDER BY data DESC
	LIMIT 20";
	$result5 = mysql_query($sel5);
	if (!$result5) { die('Invalid query5: ' . mysql_error()); }
	while(list($from,$to,$sub,$tex,$datac,$vis) = mysql_fetch_row($result5))
	{
		
		$correu_linia .='<div id="correu_f1"><p><SPAN style="font-weight: bold;"> Tema: </span>'.$sub.' 
		 <SPAN style="font-weight: bold;"> De: </span>'.$from.' <SPAN style="font-weight: bold;">A: </span>'.$to.' 
		 <SPAN style="font-weight: bold;"> Data: </span>'.$datac.'</div>
		<div id="correu_f2">'.$tex.'</div>';	
	}
	
	//


?>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<meta http-equiv="content-type" content="nt="text/; charset=UTF-8" >
		<title>escriptori ::: la coope</title>
<style type="text/css">

#correu {BACKGROUND-COLOR:#FFFFFF ; 
		BORDER: blue 2px solid ;  
		PADDING: 5px 5px 5px 5px ;
		OVERFLOW: scroll ;
		overflow-x: hidden; overflow-y: scroll;
		width: 90%;
		margin-left: auto ;
 		margin-right: auto ;
		height: 300px; 
		}
		
#correu_f1 { BACKGROUND-COLOR:#3399FF ;
		color: #FFFFFF;
		padding: 2px 2px 2px 2px; }
		
#correu_f2 { color: grey;
		padding: 4px 2px 2px 4px; 
		text-align: justify;}
		
#correu_f2 a:link { color: #000;
		text-decoration: underline; }	
#correu_f2 a:visited { color: orange;
		text-decoration: underline; }		



</style>
</head>

<body>
<div class="pagina" style="margin-top: 10px;">

<div class="contenidor_2" style="float: right;">
	<div style="border: 1px solid #C0C000; padding-bottom: 20px;">
	  <p class="h1" style="background: #C0C000;">Moneder <span <?php echo $style.">** ".$moneder; ?> **</span></p>
	  
	  <table width="80%" align="center">
		<tr><td align="center" class='cos' style="text-transform: uppercase;">Darrers moviments comptabilitzats</td></tr>
		<tr><td align="center"><table cellpadding="2" class='cos'><?php echo $last; ?></table>
		</td></tr>  
	  </table>  
	  <?php echo $nota21; ?>
	</div>
	<!-- 30 Panxes - Nov 2016: eliminació de l'apartat Agenda-->
	<div style="border: 1px solid #933a82; margin-top: 25px; padding-bottom: 20px;">
  		<p class="h1" style="background: #f47216;">Correus</p>
 		 <div id="correu">
   		 <?php echo $correu_linia; ?>
  		</div>
	</div>

</div>
<div class="contenidor_2" style="float: left;" >
	<div style="border: 1px solid #8cf800; padding-bottom: 20px;">
 		 <p class="h1" style="background: #8cf800;">Processos de comanda oberts</p>
 		 <?php 
 		 if ($moneder < -150) {
 		 	echo "<p class=\"h1\"><span style=\"text-decoration: blink; color: red;\">";
 		 	echo "Has d'afegir saldo al teu moneder per poder fer comandes</span></p>";
 		 } else {
	 		 echo $nota1;
	 		 echo $nota12; 
 		 }
 		 ?>
 		 <?php echo $nota13; ?>
	</div>
	<div style="border: 1px solid #f47216; margin-top:25px; padding-bottom: 20px;">

 		<p class="h1" style="background: #933a82;">Info d'Interès</p>
		<p style="text-align: left;">
		Fem i repartim cistelles Dijous de 18h a 21h.<BR><BR>
		<u><strong>Llistat de torns:</strong></u><BR>
		Cal confirmar l'assistència al llistat de torns, així com qui recull les claus i qui imprimeix el llistat. <a target="_blank" href="https://docs.google.com/spreadsheet/ccc?key=0AucJ0CYViz9SdFFFT3NpWXh5akNESnB2bExRcXFUSnc">Llistat de torns aquí</a>.
  		</p>
		<p style="text-align: left;">
		<u><strong>Documents:</strong></u><BR><UL style="text-align: left;">
		<LI><a href="https://docs.google.com/spreadsheets/d/1vur-7M7Cuv_UaBybifu43wb2rRZSLEb06PEZOihrh9s/edit?usp=sharing" style="color:#3367d6;text-decoration:none;vertical-align:middle" target="_blank">Comandes de les UCs per Proveïdor - 2017</a></LI>

		<LI><a href="https://docs.google.com/spreadsheets/d/13u9XI6wMNxr3csGhr33WWROWuY-9Cv3wagJzYRPJh5E/edit?usp=sharing" style="color:#3367d6;text-decoration:none;vertical-align:middle" target="_blank">Cistelles - Incidències 2017</a></LI>

		<LI><a href="https://drive.google.com/drive/folders/0B4howK6uux2tMUNxX2F3Ykk5NWM?usp=sharing" style="color:#3367d6;text-decoration:none;vertical-align:middle" target="_blank">GUIES I PROCEDIMENTS</a></LI>

		<LI><a target="_blank" href="https://docs.google.com/spreadsheet/ccc?key=0AucJ0CYViz9SdHNYU0hHeUphTnZLZGJ1ODRMRVFYNFE">Dades de les unitats de consum</a></LI>
		<LI><a target="_blank" href="http://opcions.org/sites/default/files/pdf/CalendariTemporada.pdf">
			Consulta els productes de temporada</a></LI>
		</UL>	
		</p>
		<p style="text-align: left;">
		<u><strong>Correus electrònics:</strong></u><BR><UL style="text-align: left;">
		<LI><em>general:</em> cooperativalasagrera@gmail.com</LI>
		<LI><em>tresoreria:</em> tresoreria.coopsagrera@gmail.com</LI>
		<LI><em>proveïdors-Cal Roio i La Tavella:</em> proveidors.coopsagrera@gmail.com</LI>
		<LI><em>proveïdors-Conreu Sereny:</em> prov.conreu@gmail.com</LI>
		<LI><em>proveïdors-Quèviure:</em> prov.queviure@gmail.com</LI>
		</UL>
  		</p>

	</div>
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
