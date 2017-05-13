<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];
$superuser=strtoupper($_SESSION['user']);
$_SESSION['codi_cistella'] = 'in';

$pfam=$_POST['fam'];
$pdatas=$_POST['datas'];
$pdatai=$_POST['datai'];

$gcont=$_GET['id2'];
$gfam= $_GET['id3'];
$gpfam=$_GET['id4'];
$gpdatas=$_GET['id5'];
$gpdatai=$_GET['id6'];

if ($gcont!="")
{
$pfam=$gpfam;
$pdatas=$gpdatas;
$pdatai=$gpdatai;
}

$superpfam=strtoupper($pfam);

include 'config/configuracio.php';
?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>llista comandes ::: la coope</title>
				 <!-- calendar stylesheet -->
  <link rel="stylesheet" type="text/css" media="all" href="calendar/calendar-green.css" title="win2k-1" />
  <!-- main calendar program -->
  <script type="text/javascript" src="calendar/calendar.js"></script>

  <!-- language for the calendar -->
  <script type="text/javascript" src="calendar/lang/calendar-cat.js"></script>

  <!-- the following script defines the Calendar.setup helper function, which makes
       adding a calendar a matter of 1 or 2 lines of code. -->
  <script type="text/javascript" src="calendar/calendar-setup.js"></script>

	</head>


<body>
<div class="pagina" style="margin-top: 10px;">

<?php
if ($gfam!="")
{
	$title1='Les meves comandes';
	$cap='les meves comandes';
	$cap_link='comandes.php?id3='.$user;
	$pfam=$gfam;
}
else
{
	$title1='Llistat de comandes';	
	$cap='llistat de comandes';
	$cap_link='comandes.php';	
}
?>

<div class="contenidor_1" style="border: 1px solid green;">
<p class='path'> 
><a href='admint.php'>administració</a> 
>><a href='<?php echo $cap_link; ?>'><?php echo $cap; ?></a>
</p>
<p class="h1" style="background: green; text-align: left; padding-left: 20px;"><?php echo $title1; ?></p>

<table width="80%" align="center">
<form action="<?php echo $cap_link; ?>" method="post" name="prod" id="prod">
<tr style="padding-top: 10px;">
<td width="33%" align="center" class="form">Familia</td>
<td width="33%" align="center" class="form">Superior a la data</td>
<td width="33%" align="center" class="form">Inferior a la data</td>
</tr>

<tr style="padding-bottom: 10px;">
<td align="center">

<?php
if ($gfam!="")
{	
?>
<input type="text" value="<?php echo $gfam; ?>" name="fam" id="fam" size="10" maxlength="30" readonly />
<?php
}
else
{	
?>
<SELECT name="fam" id="fam" size="1" maxlength="30" onChange="this.form.submit()">
<option value="">elegeix una familia</option>
<?php
	$select3= "SELECT nom FROM usuaris ORDER BY nom";
	$query3=mysql_query($select3);
	if (!$query3) {die('Invalid query3: ' . mysql_error());}
	while (list($sfam)=mysql_fetch_row($query3)) 
	{
		if ($pfam==$sfam){echo '<option value="'.$sfam.'" selected>'.$sfam.'</option>';}
		else {echo '<option value="'.$sfam.'">'.$sfam.'</option>';}
	}
}
?>
</td>
<td align="center">
<input type="text" value="<?php echo $pdatas; ?>" name="datas" id="f_date_a" size="8" maxlength="10" readonly />
<button type="text" name="budi" id="f_trigger_a">...</button>
<button type="submit" name="okds" id="okds">ok</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_a",     // id of the input field
        ifFormat       :    "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
        button         :    "f_trigger_a",  // trigger for the calendar (button ID)
        singleClick    :    true
    });
</script>
</td>

<td align="center">
<input type="text" value="<?php echo $pdatai; ?>" name="datai" id="f_date_b" size="8" maxlength="10" readonly />
<button type="text" name="budf" id="f_trigger_b">...</button>
<button type="submit" name="okdi" id="okdi">ok</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_b",     // id of the input field
        ifFormat       :    "%d/%m/%Y",     // format of the input field (even if hidden, this format will be honored)
        button         :    "f_trigger_b",  // trigger for the calendar (button ID)
        singleClick    :    true
    });
</script>
</td>
</form>
</tr></table>


<div class="contenidor_fac" style="border: 0px solid green;">

<?php
if ($pfam!="" OR $pdatas!="" OR $pdatai!="")
{
	$datas2 = explode ("/",$pdatas);
	$datai2 = explode ("/",$pdatai);
	$datasup = $datas2[2]."-".$datas2[1]."-".$datas2[0];
	$datainf = $datai2[2]."-".$datai2[1]."-".$datai2[0];
	
	if ($pfam!="" AND $pdatas=="" AND $pdatai=="") {$where="WHERE usuari='".$pfam."'"; $title="Recerca per família ".$superpfam;}
	elseif ($pfam!="" AND $pdatas!="" AND $pdatai=="")	{$where="WHERE usuari='".$pfam."' AND data>='".$datasup."'"; $title="Recerca per família ".$superpfam." i per data superior a ".$pdatas;}
	elseif ($pfam!="" AND $pdatas=="" AND $pdatai!="")	{$where="WHERE usuari='".$pfam."' AND data<='".$datainf."'"; $title="Recerca per família ".$superpfam." i per data inferior a ".$pdatai;}
	elseif ($pfam!="" AND $pdatas!="" AND $pdatai!="")	{$where="WHERE usuari='".$pfam."' AND  data>='".$datasup."' AND data<='".$datainf."'"; $title="Recerca per família ".$superpfam." per data entre ".$pdatas." i ".$pdatai;}
	elseif ($pfam=="" AND $pdatas!="" AND $pdatai=="")	{$where="WHERE data>='".$datasup."'"; $title="Recerca per data superior a ".$pdatas;}
	elseif ($pfam=="" AND $pdatas!="" AND $pdatai!="")	{$where="WHERE data>='".$datasup."' AND data<='".$datainf."'"; $title="Recerca per data entre ".$pdatas." i ".$pdatai;}
	elseif ($pfam=="" AND $pdatas=="" AND $pdatai!="")	{$where="WHERE data<='".$datainf."'"; $title="Recerca er data inferior a ".$pdatai;}
}
else 
{
	$where=""; 
	$title="Ordernació per numero de comanda descendent";
}

print ('<p class="h1" 
		style="background: green; font-size:14px; text-align: left; 
		height: 20px; padding-left: 20px;">
		'.$title.'
		</p>');
	 
print('<table width="100%" align="center" cellspading="5" cellspacing="5" >
		<tr class="cos_majus"><td align="center" width="10%">NUMERO</td>
		<td align="center" width="15%">USUARI</td>
		<td align="center" width="15%">PROCES-GRUP</td>
		<td align="center" width="10%">DATA recollida/fi periode</td>');

print ('<td align="center" width="10%">FACTURA</td>
		<td align="center" width="10%">DATA cistella</td>
		<td align="center" width="10%">VALIDA FAMILIA</td>
		<td align="center" width="10%">VALIDA ECONOMIA</td>');
		
print('</tr>');

	$sel = "SELECT numero FROM comanda ".$where;
	$result = mysql_query($sel);
	if (!$result) {die('Invalid query: ' . mysql_error());}
	$rnum=mysql_num_rows($result);

	if (!$gcont) {$cont=30;}
	else {$cont=$gcont;}
	
	$ordre='DESC';
	
	$sel2 = "SELECT numero,usuari,proces,grup,data,check0,report0,data2,check1,check2,notes
	FROM comanda ".$where." 
	ORDER BY numero ".$ordre." LIMIT ".$cont;
	$result2 = mysql_query($sel2);
	if (!$result2) {die('Invalid query2: ' . mysql_error());}
	
	$k=0;
	while (list($numero,$fam,$proces,$grup,$data,$check0,$report0,$data2,$check1,$check2,$notes)= mysql_fetch_row($result2))
	{
		$datarc = explode ("-",$data);
		$datavis = $datarc[2].'-'.$datarc[1].'-'.$datarc[0];
		$data_c = explode ("-",$data2);
		$data_c_vis = $data_c[2].'-'.$data_c[1].'-'.$data_c[0];
		if ($data_c_vis=="00/00/0000") $data_c_vis="";
		print('<tr class="cos">
				<td align="center"><a href="cmda2.php?id='.$proces.'&id2='.$numero.'&id4=vis">'.$numero.'</a></td>
				<td align="center">'.$fam.'</td>
				<td align="center">'.$proces.'-'.$grup.'</td>
				<td align="center">'.$datavis.'</td>');
	
		$accept0=""; $accept1=""; $accept2="";
		if ($check0==0)
		{ 
			$accept0="Pendent";
		}
		else 
		{
			$accept0='<a href="factura.php?id='.$numero.'">veure</a>';
			if ($check1=='0')
			{
				if ($fam==$user)
				{				
					//$accept1="<a href='factura.php?id=".$numero."&id2=".$report0."&id3=1'>validar</a>";
				}
				else
				{
					//$accept1="Pendent";					
				}
			}
			else 
			{
				$accept1="ok";
				if ($check2=='0')
				{
					$accept2="Pendent";
				}
				else 
				{
					$accept2="ok";
				}
			}
		}
		print('<td align="center">'.$accept0.'</td>
				<td align="center">'.$data_c_vis.'</td>
				<td align="center">'.$accept1.'</td>
				<td align="center">'.$accept2.'</td></tr>');
		$k++;
	}
	print ('</table></div></div>');
	
	if ($rnum>$cont)
	{
		$id=$cont+30;
		echo '<p><input class="button2" type="button" name="mes" value= "30+"
			onClick="javascript:window.location = \'comandes.php?id2='.$id.'&id4='.$pfam.'&id5='.$pdatas.'&id6='.$pdatai.'\'">
			</p>';
	}
	
?>
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
