<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];


?>

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<title>ajuda ::: la coope</title>
	</head>
	
<body>

<div class="pagina" style="margin-top: 10px;">
	<div class="contenidor_1" style="border: 1px solid #933a82;">
	  <p class="h1" style="background: #933a82; text-align: left; padding-left: 20px;">Ajuda</p>

  <ul type="circle" style="text-align: left; padding-left: 40px;">
  <li><a href="http://vimeo.com/channels/629250/79835667" target="_blank">Fer la comanda</a></li>
  <li><a href="http://vimeo.com/channels/629250/79836983" target="_blank">Demanar la comanda als proveïdors</a></li>
  <li><a href="http://vimeo.com/channels/629250/79836986" target="_blank">Fer cistelles</a></li>
  <li><a href="http://vimeo.com/channels/629250/79836984" target="_blank">Processos oberts de comanda</a></li>
  <li><a href="http://vimeo.com/channels/629250/79916802" target="_blank">Eines de comunicació interna</a></li>
  <li><a href="" target="_blank">Funcions d'economia</a></li>
  <li><a href="http://vimeo.com/channels/629250/79836988" target="_blank">Funcions d'administració d'usuaris</a></li>
  <li><a href="http://vimeo.com/channels/629250/79836989" target="_blank">Funcions de gestió d'estoc i proveïdores</a></li>  
  </ul>
  <p></p>

</div>
</div>
</body>
</html>

<?php 

} 
else {
header("Location: index.php"); 
}
?>