<?php

if(isset($_POST['user'])) 		$user		= $_POST['user'];
else 							$user		= "";
if(isset($_POST['yhtio'])) 		$yhtio		= $_POST['yhtio'];
else 							$yhtio		= "";
if(isset($_POST['salamd5'])) 	$salamd5	= $_POST['salamd5'];
else 							$salamd5	= "";
if(isset($_POST['salasana'])) 	$salasana	= $_POST['salasana'];
else 							$salasana	= "";
if(isset($_POST['uusi1'])) 		$uusi1		= $_POST['uusi1'];
else 							$uusi1		= "";
if(isset($_POST['uusi2'])) 		$uusi2		= $_POST['uusi2'];
else 							$uusi2		= "";

require ("inc/functions.inc");

if ($user != '') {	//kayttaja on syottanyt tietonsa login formiin

	$login="yes";
	require("inc/parametrit.inc");

	$session = "";
	srand ((double) microtime() * 1000000);

	$query = "	SELECT kuka, session, salasana
				FROM kuka
				where kuka='$user'";
	$result = mysql_query($query) or die("Kysely epäonnistui");
	$krow = mysql_fetch_array ($result);

	if($salamd5!='')
		$vertaa=$salamd5;
	elseif($salasana == '')
		$vertaa=$salasana;
	else
		$vertaa = trim($salasana);
		//$vertaa = md5(trim($salasana));

	if ((mysql_num_rows ($result) > 0) and ($vertaa == $krow['salasana'])) {

		// jos meillä on vaan kaks yhtiotä ja ollaan tulossa firman vaihdosta, vaihdetaan suoraan toiseen
		if (mysql_num_rows($result) == 2 and $mikayhtio != "") {

			mysql_data_seek($result,0); // ressu alkuun

			while ($vaihdarow = mysql_fetch_array($result)) {

				if ($mikayhtio != $vaihdarow["yhtio"]) {
					$krow = $vaihdarow;
					$yhtio = $vaihdarow["yhtio"];
					$usea = 0;
				}
			}
		}

		// Onko monta sopivaa käyttäjätietuetta == samalla henkilöllä monta yritystä!
		if (mysql_num_rows($result) > 1) {
			$usea = 1;
		}

		if (strlen(trim($uusi1)) > 0)
		{
			if (trim($uusi1) != trim($uusi2))
			{
				$errormsg = t("Uudet salasanasi olivat erilaiset")."! ".t("Salasanaasi ei vaihdettu")."!";
				$err = 1;
				$usea = 0;
			}
			else {
				//$uusi1=md5(trim($uusi1));
				$uusi1=trim($uusi1);
				
				$query = "	UPDATE kuka
							SET salasana = '$uusi1'
							WHERE kuka = '$user'";
				$result = mysql_query($query)
					or die ("Salasanapäivitys epäonnistui kuka");

				$vertaa=trim($uusi1);
				$salasana=trim($uusi2);
				$errormsg = t("Salasanasi vaihdettiin onnistuneesti")."!";
			}
		}

		// Kaikki ok!
		if (!isset($err) or $err != 1) {
			// Pitääkö vielä kysyä yritystä???
			if ($usea != 1 or strlen($yhtio) > 0) {
				for ($i=0; $i<25; $i++) {
					$session = $session . chr(rand(65,90)) ;
				}
				$query = "	UPDATE kuka
							SET session = '$session',
							lastlogin = now()
							WHERE kuka = '$user'";
				if (strlen($yhtio) > 0) {
					$query .= " and yhtio = '$yhtio'";
				}
				$result = mysql_query($query) or die ("Päivitys epäonnistui kuka $query");

				$bool = setcookie("pupesoft_session", $session, time()+43200); // 12 tuntia voimassa

				if ($bool === FALSE) {
					$errormsg = t("Selaimesi ei ilmeisesti tue cookieta",$browkieli).".";
				}
				else {
					echo "<META HTTP-EQUIV='Refresh'CONTENT='0;URL=main.php'>";
					exit;
				}
			}
		}
	}
	else {
		$errormsg = t("Käyttäjätunnusta ei löydy ja/tai salasana on virheellinen",$browkieli)."!";
	}
}
$formi = "login"; // Kursorin ohjaus
$kentta = "user";

echo "
<html>
	<head>
	<title>Login</title>
	<meta http-equiv='Pragma' content='no-cache'>
	<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
	</head>";
	
echo "<STYLE TYPE='text/css'>
	<!--
    
	* {
		font-size: 10pt;
		font-weight:normal;
		font-family: sans-serif; 
		color: #555;  
	}
	body {
		background-color: #ffffff;
	}
	form {
		margin: 0px;
		padding: 0px;
		background-color: inherit;
	}
	table {
		border-spacing: 1px 1px;
		display: table;
		empty-cells: show;
		background-color: inherit;
	}
	table.main {
		border-spacing: 1px 1px;
		border: 1px #88adeb solid;
		padding: 4px;
		display: table;
		width: 700px;
		empty-cells: show;
		background-color: inherit;
	}
	a {
		color: #333;
		background-color: inherit; 
	}
	a.td {
		color: #333;
		font-size: 8pt;
		text-align: center;
		text-decoration: none;
		background-color: inherit;
		display: block; 
	}
	a.menu {
		color: #333;
		text-decoration: none;
		white-space: nowrap;
		font-stretch: condensed;
		display: block;
		width: 100%;
		background-color: inherit;
	}
	a:hover {
		border-bottom: 1px solid #ff0000;
	}
	a.td:hover {
		background-color: #00FF00;
		border-bottom: 0px;
	}
	a.menu:hover {
		background-color: #eee;
		border-bottom: 0px;
	}
	a.puhdas:hover {
		border-bottom: 0px;
	}
	th {
		color: #333; 
		background-color: #88adeb; 
	}
	th a {
		color: #333;
	}
	td {
		color: #333;
		font-size: 8pt;
		background-color: #eee;
	}
	td.tumma {
		color: #333; 
		background-color: #88adeb; 
	}
	td.day {
		color: #333; 
		padding: 4px;
		text-align:center;
		vertical-align: top;
		border: 1px solid #005A9B;
		background-color: #88adeb; 
	}
	td.green {
		color: #333;
		text-align: center;
		width: 35px; 
		height: 20px;
		background-color: #afa;

	}
	td.red {
		color: red; 
		background-color: #faa;
	}
	td.spec {
		color: #FF0000; 
		background-color: #eee;
	}
	td.back {
		color: #333;
		background-color: #ffffff; 
	}
	input, textarea, select {
		margin: 0x;
		padding: 2px;
		background-color: #FFFFFF; 
		border: 1px #88adeb solid; 
	}
	hr {
		border: 0px;
		height: 1px;
		background-color: #88adeb;
		width: 100%; 
	}
	h1 {
		color: #005A9B; 
		text-transform: uppercase;
		font-weight:bold;
		letter-spacing: 3px;
		background-color: inherit;
		display: inline;
	}
	font.info {
		font-size:8pt;  
		color: #005A9B;
		font-weight: normal;
		background-color: inherit;
	}   
	font.head {
		color: #005A9B; 
		text-transform: uppercase;
		font-weight:bold;
		letter-spacing: 3px;
		background-color: inherit;
	}
	font.menu {
		font-size:9pt;  
		color: #005A9B;
		background-color: inherit;
	}
	font.error {
		font-size:9pt;  
		color: red; 
		font-weight:bold;
		background-color: inherit;
	}   
	font.message {
		font-size:9pt;  
		color: green; 
		font-weight:bold;
		background-color: inherit;
	}					
	-->
	</STYLE>";
	
	
echo "<table>";
echo "<table class='main' align='center'>";
echo "<tr><td class='back' valign='bottom' nowrap><img src='logo2.gif'></td><td class='back' nowrap><h1>MRC - Varauskalenteri: ". $MONTH_ARRAY[$month] ." $year</h1></td></tr>";
echo "<tr>
<td valign='top' class='back'><br><a target='_top' href='/'><img src='njkmrc.jpg' border='0'></a></td>
<td class='back'><font class='menu'>Sisäänkirjautuminen</font><br><br>

";

if (isset($usea) and $usea == 1) {
	$query = "	SELECT yhtio.nimi, yhtio.yhtio
				FROM kuka, yhtio
				WHERE kuka='$user' and yhtio.yhtio=kuka.yhtio 
				ORDER BY nimi";
	$result = mysql_query($query)
		or die ("Kysely ei onnistu $query");

	if (mysql_num_rows($result) == 0) {
		echo t("Sinulle löytyi monta käyttäjätunnusta, muttei yhtään yritystä",$browkieli)."!";
		exit;
	}

	echo "<table class='login'>";
	echo "<tr><td colspan='2'><font class='menu'>".t("Valitse käsiteltävä yritys",$browkieli).":</font></td></tr>";
	echo "<tr>";

	while ($yrow=mysql_fetch_array ($result)) {
		for ($i=0; $i<mysql_num_fields($result)-1; $i++) {
			echo "<td><font class='menu'>$yrow[$i]</font></td>";
		}
		echo "<form action = 'login.php' method='post'>";
		
		if (isset($errormsg)) {
			echo "<input type='hidden' name='errormsg' value='$errormsg'>";
		}
		
		echo "<input type='hidden' name='user'     value='$user'>";
		echo "<input type='hidden' name='salamd5' value='$vertaa'>";
		echo "<input type='hidden' name='yhtio'    value='$yrow[yhtio]'>";
		echo "<td><input type='submit' value='".t("Valitse")."'></td></tr></form>";
	}
	echo "</table><br>";

	if (isset($errormsg) and $errormsg != "") {
		echo "<font class='error'>$errormsg</font><br><br>";
	}
	echo "<font class='info'>Copyright &copy; 2002-".date("Y")." <a href='http://www.pupesoft.com/'>pupesoft.com</a> - <a href='license.php'>Licence Agreement</a></font>";
}
else {

	echo "<table>
			<form name='login' target='_top' action='index.php' method='post'>

			<tr><td><font class='menu'>".t("Käyttäjätunnus",$browkieli).":</font></td><td><input type='text' value='' name='user' size='15' maxlength='30'></td></tr>
			<tr><td><font class='menu'>".t("Salasana",$browkieli).":</font></td><td><input type='password' name='salasana' size='15' maxlength='30'></td></tr>

			<tr><td colspan='2'><font class='menu'>".t("Jos haluat vaihtaa salasanasi",$browkieli).",<br>".t("anna se kahteen kertaan alla olevin kenttiin",$browkieli)."</font></td></tr>

			<tr><td><font class='menu'>".t("Uusi salasana",$browkieli).":</font></td><td><input type='password' name='uusi1' size='15' maxlength='30'></td></tr>
			<tr><td><font class='menu'>".t("ja uudestaan",$browkieli).":</font></td><td><input type='password' name='uusi2' size='15' maxlength='30'></td></tr>
		</table>";
	
	if (isset($errormsg) and $errormsg != "") {
			echo "<br><font class='error'>$errormsg</font><br>";
	}
	
	echo "	<br><input type='submit' value='".t("Sisään",$browkieli)."'>
			<br><br>
			<font class='info'>Copyright &copy; 2003-".date("Y")." Johan Tötterman</font>
			</form>";
}

echo "</td></tr></table>";
echo "<script LANGUAGE='JavaScript'>window.document.$formi.$kentta.focus();</script>";
echo "</body></html>";

?>