<?php

session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

$user = $_SESSION['user'];

include 'config/configuracio.php';

$sel="SELECT tipus FROM usuaris WHERE nom='$user'";
$query=mysql_query($sel) or die ('query failed: '.mysql_error());
list($priv)=mysql_fetch_row($query);

	if ($priv!='user')
	{
	$h1= "";
	}
	else
	{
	$h1= "href=''";
	}

	if ($priv=='admin' OR $priv=='super')
	{
	$h2="href='editfamilies3.php'";
	}
	else
	{
	$h2="href=''";
	}

	if ($priv=='eco' OR $priv=='super')
	{
	$h4="href='moneder_linia.php'";
	$h5="href='devolucions.php'";
	}
	else
	{
	$h4="href=''";
	$h5="href=''";
	}

?>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
		<title>administracio ::: la coope</title>

<style type="text/css">
a:link{color:black; }
a:visited{color:red; }
a:hover{color:white; background-color:orange; font-weight: bold; border: 4px solid orange;}
a:active{color:white; background-color:orange; font-weight: bold;  border: 4px solid orange;}
</style>
</head>

<body>

<div class="pagina" style="margin-top: 10px;">
	<div class="contenidor_1" style="border: 1px solid red;">
	  <p class="h1" style="background: red; text-align: left; padding-left: 20px;">Administració</p>

<table cellspacing="25" cellpadding="10" style="padding:0 30 0 30;">
<tr>
<td valign="top" align="left" width="33%">
  <p class="cos16">Comandes</p>
  <ul type="circle">
  <li><a class="cos" href='grups_comandes.php'>Grups de comandes i cistelles</a></li>
  <li><a class="cos" href='comandes.php'>Llista de comandes i factures</a></li>
<?php if ($priv=='eco' OR $priv=='super') { ?>
  <li><a class="cos" <?php echo $h5; ?>>Devolucions i factures fora procés</a></li>
<?php } ?>
  </ul>
  <p class="cos16">Famílies</p>
  <UL type="circle">
  <li><a class="cos" href='families.php'>Llista famílies</a></li>
<?php if ($priv=='admin' OR $priv=='super') {?>
      <li><a class="cos" <?php echo $h2; ?>>Crear i editar famílies</a></li>
<?php } ?>
    
  </UL>
  <p class="cos16">Comunicacions</p>
  <UL type="circle">
  <li><a class="cos" href='notes.php'>Introduir notes a l'escriptori</a></li>
  <!-- Comentat per 30 Panxes
  <li><a class="cos" href='cistella_incidencia.php'>Comunicació incidències</a></li>
  -->
  </UL>
  <p class="cos16">Moneder</p>
  <UL type="circle">
<?php if ($priv=='eco' OR $priv=='super') { ?>
  <li><a class="cos" <?php echo $h4; ?>>Introduir línia</a></li>
<?php } ?>
  <li><a class="cos" href="comptes.php">Història moviments</a></li>
  <li><a class="cos" href="moneder_usuari.php">Llista moneder famílies</a></li>
  </UL>
  </td>
  
  <td valign="top" align="left" width="33%">
  <p class="cos16">Processos</p>
  <UL type="circle">
  <li><a class="cos" href='editprocessos.php'>Crear, editar, eliminar processos</a></li>
  <li><a class="cos" href='associar.php'>Associar processos, grups i categories</a></li>
  </UL>
  <p class="cos16">Grups</p>
  <UL type="circle">
    <li><a class="cos" href='editgrups.php'>Crear, editar, eliminar grups</a></li>
  </UL>
  <p class="cos16">Informes</p>
  <UL type="circle">
    <li><a class="cos" href='./reportico-4.6/Codi_Cistella_Tancada.php?clear_session=1'>Codi de Cistella Tancada</a></li>
    <li><a class="cos" href='./reportico-4.6/run.php?project=Aplicoop&execute_mode=MENU'>Llistats d'Informes (pwd: Massadas)</a></li>
  </UL>
   <p class="cos16">Categories i subcategories</p>
  <UL type="circle">
  <li><a class="cos" href='categories.php'>Crear, editar, eliminar categories i subcategories</a></li>
  </UL> 
   <p class="cos16">Estadística</p>
  <UL type="circle">
  <li><a class="cos" href='estat_consum.php'>Estadística consum</a></li>
  <li><a class="cos" href='estat_iva.php'>Consum IVA</a></li>
  </UL>
  </td>
  
  <td valign="top" align="left" width="33%"> 
	<p class="cos16">Productes</p>
  <UL type="circle">
  <li><a class="cos" href='baixa_productes.php'>Activar/desactivar productes</a></li>
  <li><a class="cos" href='productes.php'>Crear, editar, eliminar productes</a></li>
  <li><a class="cos" href='canvi_massiu_productes.php'>Canviar preus, iva i marge en llistat</a></li>
  </UL>  
  <p class="cos16">Proveïdores</p>
  <UL type="circle">
  <li><a class="cos" href='proveidores.php'>Crear, editar, eliminar proveïdores</a></li>

  </UL>
  <!--
  Modificat per 30Panxes - December 2016
  <p class="cos16">Albarans</p>
  //-->
   <p class="cos16">Albarans d'entrada per productes amb estoc</p>
  <UL type="circle">
  <li><a class="cos" href='albarans.php'>Crear, editar, eliminar albarans</a></li>
  </UL>
  
  <p class="cos16">Estoc</p>
  <UL type="circle">
  <li><a class="cos" href='inventari2.php'>Veure estoc actual</a></li>
  </UL>

</td>
</tr>
</table>
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
