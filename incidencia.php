<?php 

// $email_incidencies i $logo_portada provenen de l'arxiu de configuració
include 'config/configuracio.php';

$site_admin = 'XXXXXXXXX@XXXXXX.XXX';

// function ae_send_mail (see code above) is pasted here
function ae_send_mail($from, $to, $subject, $text, $headers="")
{
    if (strtolower(substr(PHP_OS, 0, 3)) === 'win')
        $mail_sep = "\r\n";
    else
        $mail_sep = "\n";

    function _rsc($s)
    {
        $s = str_replace("\n", '', $s);
        $s = str_replace("\r", '', $s);
        return $s;
    }

    $h = '';
    if (is_array($headers))
    {
        foreach($headers as $k=>$v)
            $h = _rsc($k).': '._rsc($v).$mail_sep;
        if ($h != '') {
            $h = substr($h, 0, strlen($h) - strlen($mail_sep));
            $h = $mail_sep.$h;
        }
    }

    $from = _rsc($from);
    $to = _rsc($to);
    $subject = _rsc($subject);
    mail($to, $subject, $text, 'From: '.$from.$h);
}


if (($_SERVER['REQUEST_METHOD'] == 'POST') &&
    isset($_POST['from']) && isset($_POST['text'])) 
    {
        $subject ='incidencia de la coope';
        // nice RFC 2822 From field
        ae_send_mail($_POST['from'], $email_incidencies, $subject, $_POST['text'],
        array('X-Mailer'=>'PHP script at '.$_SERVER['HTTP_HOST']));
        $mail_send = true;
        
    }
?>

<html>
<head>
<title>incidències ::: la coope</title>
<meta http-equiv="content-type" content="="text/ht; charset=UTF-8" >
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

if (isset($mail_send)) {
    echo '<div class="contenidor_1" style="padding-top: 100px;"
    <p class="error">La incidència ha estat enviada.</p>
    <p class="error">En breu intentarem solventar-ho.</p>
    <p class="error">Gràcies per la vostra col·laboració.</p>
    </div>';
   }
else {
?>

<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="frmIncidencies" id="frmIncidencies">

<div class="contenidor_1" style="padding:150px 0px 40px;">
	<img style="width: 340px; height: 160px;" src="<?php echo $logo_portada; ?>">
</div>

<div class="contenidor_1" style="">
	<p class="form" style="text-align: center;">
	nom de la família <input name="from" type="text" id="txtUserInc">
	</p>
	<p class="form" style="text-align: center;">
	<span style="vertical-align: top;">incidència </span>
	<textarea style="overflow: visible;" name="text" cols="60" rows="3" id="txtPassword"></textarea>
	</p>
	
	<div style="padding-top: 40px;">
		<p class="form" style="text-align: center;">
		<input name="btnIncidencies" type="submit" id="btnIncidencies" value="envia" class="button">
		</p>
	</div>
</div>

<div class="contenidor_1" style="padding-top: 50px;">
	<img style="width: 955px;" src="imatges/senefa.jpg">
	<p class="cos2" style="text-align: center;">
	envia la incidència i en breu s'intentarà sol·lucionar
	</p>
</div>
</div>
</form>
<?php } ?>
</body>
</html>
