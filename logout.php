<?php

session_start();
$user = $_SESSION['user'];
$sessionid=$_SESSION['sessionid'];

date_default_timezone_set('Europe/Andorra');
$avui=getdate();
$timefise=$avui['year'].'-'.$avui['mon'].'-'.$avui['mday'].' '.$avui['hours'].':'.$avui['minutes'].':'.$avui['seconds'];

include 'config/configuracio.php';

$sql2 = "UPDATE session SET date2='$timefise'
  		   WHERE sessionid='$sessionid'";
mysql_query($sql2) or die('Query2 failed. ' . mysql_error());

header ('Location: index.php');

include 'config/disconect.php';

session_unset();
session_destroy();

exit;
?>