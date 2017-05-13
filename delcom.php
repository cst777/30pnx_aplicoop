<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user=strtoupper($_SESSION['user']);

$numcmda=$_GET['id'];


include 'config/configuracio.php';

$query = "DELETE FROM comanda_linia WHERE numero='$numcmda'";
mysql_query($query) or die('Error, insert query failed');

$numrows = mysql_affected_rows();

$query3 = "DELETE FROM comanda WHERE numero='$numcmda'";
mysql_query($query3) or die('Error, insert query failed');



?>

<html>
	<head>
		<title>borrar la comanda ::: la coope</title>
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	</head>
<body>
<div class="pagina" style="margin-top: 10px;">
	<div class="contenidor_1" style="border: 1px solid #9cff00;"> 
		<p class="error" style="padding-top: 50px; font-size: 16px;">
				La comanda numero <?php echo $numcmda; ?>
				ha estat borrada completament.
		</p>
		<P style="font-size: 14px; color: grey; align-text: center;">
		[<?php echo $numrows; ?> productes borrats]
		</p>		
		</p>
		<p class="error" style="font-size: 14px; padding-bottom: 50px;">
		<a href="escriptori2.php" target="cos" title="clica per tornar a l'escriptori">		
				Torna a l'escriptori</a>
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


