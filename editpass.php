<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];
$superuser=strtoupper($_SESSION['user']);

/*30 Panxes*/
$nom = $_GET['id'];
/* End 30 Panxes*/


$click=$_POST['click'];
$password=$_POST['password'];
$password2=$_POST['password2'];
include 'config/configuracio.php';

?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>editar clau de pas ::: la coope</title>
	</head>
	
<body>

<div class="pagina" style="margin-top: 10px;">
<div class="contenidor_1" style="border: 1px solid orange;">

<p class='path'> 
><a href='admint.php'>administració</a>
>><a href='editfamilies3.php'>crear i editar famílies</a>
>>><a href='editdadesp.php?id=<?php echo $nom; ?>'>editar família <?php echo $nom; ?></a>
>>>><a href='editpass.php?id=<?php echo $nom; ?>'>canviar clau de pas</a>
</p>
<p class="h1" style="background: orange; text-align: left; padding-left: 20px;">
Canviar la clau de pas família <?php echo $nom; ?>
</p>

<?php
	if (isset($click) and $click=="change-password")
	{
		$password=mysql_real_escape_string($password);

		//Setting flags for checking
		$status = "OK";
		$msg="";
		
		if ($password=="")
		{
			echo "<p class='error' style='font-size: 14px;'>La clau no pot ser nul·la</p>";
		}
		else
		{
			if ( strlen($password) > 10 )
			{
				echo "<p class='error' style='font-size: 14px;'>La clau de pas ha de ser inferior a 10 dígits</p>";
			}
			else
			{
				if ( $password <> $password2 )
				{
					echo "<p class='error' style='font-size: 14px;'>Les dues claus de pas no coincideixen</p>";
				}
			else
				{
					// if all validations are passed.
					$md5pass=md5($password);
					$query2="update usuaris set claudepas='$md5pass' where nom='$nom'";
					mysql_query($query2) or die('Error, insert query2 failed');
					echo "<p class='error' style='font-size: 14px;'>la vostra clau de pas ha canviat</p>";
				}	
			}
		}
	}	
?>

<div class="contenidor_fac" style=" width:500px; border: 1px solid orange; margin-bottom:20px;">
<form action="editpass.php?id=<?php echo $nom; ?>" method="post" name="frmeditpass" id="frmeditpass">
<input type=hidden name=click value=change-password>

<table style="padding: 10px;" width="100%" align="center" cellspading="5" cellspacing="5" >
<tr>
<td class="cos_majus">Clau de pas</td>
<td>
<input type ='password' name='password' >
</td>
</tr>
<tr>
<td><span class="cos_majus">Clau de pas</span><span class="cos"> (repeteix per seguretat)</span></td>
<td>
<input type ='password' name='password2' >
</td>
</tr>
</table>

<p class="linia_button2" style="background: orange; text-align: center; vertical-align: middle;">
<input class="button2" type=submit value='Guarda'>
<input class="button2" type=reset value=Borra>
</p>
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