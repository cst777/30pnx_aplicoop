<?php
// we must never forget to start the session
session_start();

include 'config/configuracio.php';

$errorMessage = '';
if (isset($_POST['txtUserId']) && isset($_POST['txtPassword'])) {
   // first check if the number submitted is correct
   $number = $_POST['txtNumber'];

   if (md5($number) == $_SESSION['image_random_value']) {
      //include 'config/configuracio.php';
      
      $userId = $_POST['txtUserId'];
      $password = $_POST['txtPassword'];


      // check if the user id and password combination exist
      $sql = "SELECT nom FROM usuaris  WHERE nom = '$userId' AND claudepas ='".md5($password)."' AND tipus2='actiu'";

      $result = mysql_query($sql) or
                die('Query failed. ' . mysql_error());

      if (mysql_num_rows($result) == 1) 
      {
         // the user id and password match,
         // check if the user is already active
         
         list ($nom)=mysql_fetch_row($result);
         
         // set the session
         $_SESSION['image_is_logged_in'] = true;

         // remove the random value from session
         $_SESSION['image_random_value'] = '';
         
         // convert in minuscules 
         $userId=strtolower($userId);
         
         // Identify user
         $_SESSION['user'] = $userId;
         
         //identify date
			date_default_timezone_set('Europe/Madrid');
         $_SESSION['timeinitse']=time();
         $timeinitse= date ("Y:m:d H:i:s", $_SESSION['timeinitse']);
         
         
         //keep number session
          $sql2 = "INSERT INTO session (user, date)
          		 VALUES ('$userId', '$timeinitse')";
          mysql_query($sql2) or 
                die('Query2 failed. ' . mysql_error());
           $sessionid=mysql_insert_id();
           $_SESSION['sessionid']=$sessionid;
          
         // after login we move to the main page
         header('Location: portada.php');
         exit;
  
      }
      else {
         $errorMessage = 'Ho sentim, el nom de la família o la clau de pas son errònies. Prova altra vegada.';
         include 'config/disconect.php';
      }
   }
   else {
      $errorMessage = 'Ho sentim, el numero no és correcte. Prova altra vegada.';
   }
}

?>

<html>
<head>
<title>login ::: la coope</title>
<meta http-equiv="content-type" content="t="text/h; charset=UTF-8" 8>
	<meta http-equiv="Content-Language" content="ca" />
	<link rel="stylesheet" type="text/css" href="coope.css" />
</head>
<style type="text/css">

img {display: block;
  overflow: visible;
  text-align: center;
  margin: auto;}
  
</style>
<body>
<div class="pagina">

<?php
if ($errorMessage != '') {
?>
<p class="error"><?php echo $errorMessage; ?></p>

  <?php
}
?>

<div class="contenidor_1" style="padding:150px 0px 40px; ">
	<img style="width: 340px; height: 160px;" src="<?php echo $logo_portada; ?>">
</div>

<div class="contenidor_1">
	<form action="" method="post" name="frmLogin" id="frmLogin">
	<p class="form" style="text-align: center;">
	nom família
	<input name="txtUserId" type="text" id="txtUserId">
	&nbsp;&nbsp;clau de pas
	<input name="txtPassword" type="password" id="txtPassword">
	</p>
	<p class="form" style="text-align: center;">
	número
	<input name="txtNumber" type="text" id="txtNumber" value="">
	&nbsp;&nbsp;
	<img style="width:64px; height:32px; display: inline; vertical-align:middle; border:0;" 
	src="randomImage.php">
	</p>
	<div style="padding-top: 40px;">
		<p class="form" style="text-align: center; ">
		<input name="btnLogin" type="submit" id="btnLogin" value="entrar" class="button">
		</p>
	</div>
	</form>
</div>
<div class="contenidor_1" style="padding-top: 60px;">
	<img style="width: 955px;" src="imatges/senefa.jpg">
<!-- Comentat per 30 Panxes
	<p class="cos2" style="text-align: center;">
	per qualsevol incidència clica <a href="incidencia.php" target="_blank">aquí</a>
	</p>
 --> 
</div>
</div>
</body>
</html>
