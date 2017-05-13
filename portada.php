<?php
// we must never forget to start the session
session_start();

if ($_SESSION['image_is_logged_in'] == 'true' ) {

?>
			
<html>
<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" >
		<link rel="stylesheet" type="text/css" href="coope.css" />
      <title>portada ::: la coope</title>
</head>
<frameset rows="150,*" frameborder=0 border=0 marginheight=0 marginlength=0 topmargin=0 leftmargin=0>
	<frame name=cap src="cap.php" frameborder=0 border=0 marginheight=0 marginlength=0 topmargin=0 leftmargin=0 scrolling=no>
	<frame name=cos src="escriptori2.php" frameborder=0 border=0 marginheight=0 marginlength=0 topmargin=0 leftmargin=0>
</frameset>
<noframes></noframes>
</html>

<?php
}
else {
         header('Location: index.php');
         exit;
      }
 ?>
