<?php

if (isset($_POST['user'])) 		$user		= $_POST['user'];
else 							$user		= "";
if (isset($_POST['salasana'])) 	$salasana	= $_POST['salasana'];
else 							$salasana	= "";

require ("inc/functions.inc");

if (isset($_POST['kalenteri_kieli']) and ($_POST['kalenteri_kieli'] == "se" or $_POST['kalenteri_kieli'] == "fi")) {
	$bool = setcookie("matchrace_kieli", $_POST['kalenteri_kieli']);
	$_COOKIE["matchrace_kieli"] = $_POST['kalenteri_kieli'];
}

if ($user != '') {	//kayttaja on syottanyt tietonsa login formiin

	$login="yes";
	require("inc/parametrit.inc");

	$session = "";
	srand ((double) microtime() * 1000000);

	$query = "	SELECT kuka, session, salasana
				FROM kuka
				where kuka = '$user'";
	$result = mysql_query($query) or die("Kysely ep�onnistui");
	$krow = mysql_fetch_array ($result);

	$vertaa = trim($salasana);
	
	if (mysql_num_rows($result) > 0 and $vertaa == $krow['salasana']) {				
		// Kaikki ok!
		if (!isset($err) or $err != 1) {			
			for ($i=0; $i<25; $i++) {
				$session = $session . chr(rand(65,90)) ;
			}
			$query = "	UPDATE kuka
						SET session = '$session',
						lastlogin = now()
						WHERE kuka = '$user'";								
			$result = mysql_query($query) or die ("P�ivitys ep�onnistui kuka $query");

			$bool = setcookie("matchrace_session", $session, time()+43200);

			if ($bool === FALSE) {
				$errormsg = "Selaimesi ei ilmeisesti tue ev�steit�.";
			}
			else {
				echo "<META HTTP-EQUIV='Refresh'CONTENT='0;URL=main.php'>";
				exit;
			}
		}
	}
	else {
		$errormsg = t("K�ytt�j�tunnusta ei l�ydy ja/tai salasana on virheellinen")."!";
	}
}
$formi = "login"; // Kursorin ohjaus
$kentta = "user";

header("Content-Type: text/html; charset=iso-8859-1");

//haetaan kieli
$kalenteri_kieli = $_COOKIE["matchrace_kieli"];

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"sv-se\" lang=\"sv-se\" dir=\"ltr\" >
		<head>
			<base href=\"$palvelin2\" />
			<meta http-equiv=\"content-type\" content=\"text/html; charset=iso-8859-1\" />
			<meta name=\"robots\" content=\"index, follow\" />
			<meta name=\"keywords\" content=\"\" />
			<meta name=\"description\" content=\"\" />

			<META Http-Equiv=\"Cache-Control\" Content=\"no-cache\">
			<META Http-Equiv=\"Pragma\" Content=\"no-cache\">
			<META Http-Equiv=\"Expires\" Content=\"0\">

			<title>MatchRace / 606 / SM40 - Varauskalenterit</title>
			<link href=\"images/favicon.ico\" rel=\"shortcut icon\" type=\"image/x-icon\" />
			<link rel=\"shortcut icon\" href=\"images/favicon.ico\" />
			<link href=\"css/template_css.css\" rel=\"stylesheet\" type=\"text/css\" />
		</head>
		<body>
		<div id=\"page_margins\">
 			<div id=\"page\">
  				<div id=\"topmenu\">
			 		<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">
 					<tr>
 						<td>
 						<div id=\"topnav_corner_left\">&nbsp;</div>
 						</td>
						<td>
						<div id=\"topmenu_container\">
    					<ul class=\"menu\" id=\"mainmenu\">
						<li class=\"item1\"><a href=\"{$palvelin2}\"><span>".t("Kalenteri")."</span></a></li>";

echo "					</ul>
						</div>
    					</td>
    					<td>
    					<div id=\"topnav_corner_right\">&nbsp;</div>
 						</td>
 					</tr>
 					</table>
			 	</div>
				<!-- topmenu end -->
				<div id=\"header\">
    				<div id=\"header_image\">
						<img src=\"images/matchrace.jpg\" alt=\"matchrace.jpg\" style=\"width: 218px; height: 207px; float: left;\" title=\"matchrace.jpg\" width=\"386\" height=\"207\" />    		</div>
    					<div id=\"header_text\">
            			<table cellpadding=\"0\" cellspacing=\"0\" class=\"moduletable\">
						<tr>
						<td>";

echo "					<h3>MatchRace / 606 / SM40 - ".t("Varauskalenterit")."</h3>";

echo "					</td>
						</tr>
						</table>
	    			</div>
    				<a href=\"http://www.njk.fi\"><div id=\"logo\"></div></a>
				</div>

				<div id=\"content_wrapper\">
					<table border=\"0\" width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
		   				<tr>
		  				<td valign=\"top\" id=\"content_left_shadow\">&nbsp;</td>
		  				<td align=\"center\" valign=\"top\" id=\"content_td\">
		    				<div id=\"main_content\">";


echo "<h2>".t("Sis��nkirjautuminen").":</h2>";

echo "<form name='login' target='_top' action='index.php' method='post'>";
echo "<table class='main'>";

echo "<tr><td style='width: 50%'><font class='menu'>".t("K�ytt�j�tunnus").":</font></td><td style='width: 50%'><input type='text' value='' name='user' size='15' maxlength='30'></td></tr>
		<tr><td style='width: 50%'><font class='menu'>".t("Salasana").":</font></td><td style='width: 50%'><input type='password' name='salasana' size='15' maxlength='30'></td></tr>
		</table>";

if (isset($errormsg) and $errormsg != "") {
	echo "<br><font class='error'>$errormsg</font><br>";
}

echo "<br><input type='submit' value='".t("Sis��n")."'>";
echo "</form><br><br>";

echo "<form name='login' target='_top' action='index.php' method='post'>";
echo "<table class='main'>";
echo "<tr><td style='width: 50%'><font class='menu'>V�lj srp�k / Valitse kieli</font></td><td style='width: 50%'>";
echo "<select name='kalenteri_kieli' onchange='submit();'>";
echo "<option value=''>".t("Valitse kieli")."</option>";
echo "<option value='se'>Svenska</option>";
echo "<option value='fi'>Suomi</option>";
echo "</select>";
echo "</td></tr>";
echo "</table>";
echo "</form>";

echo "<br><br><font class='info'>Copyright &copy; 2003-".date("Y")." Johan T�tterman</font>";
echo "<script LANGUAGE='JavaScript'>window.document.$formi.$kentta.focus();</script>";
echo "</div></td><td valign='top' id='content_right_shadow'>&nbsp;</td></tr></table></div></body></html>";

?>