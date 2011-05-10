<?php

// poistetaan hipsuja ja trimmataan kaikki get muuttujat!
$serc = array("\"","'","<",">","\\", ";");
$repl = array("", "", "", "", "", "");

foreach ($_GET as $key => $value) {
	$_GET[$key] = str_replace($serc, $repl, $_GET[$key]);
	${$key} = str_replace($serc, $repl, $_GET[$key]);
}

// poistetaan hipsuja ja trimmataan kaikki post muuttujat!
foreach ($_POST as $key => $value) {
	$_POST[$key] = str_replace($serc, $repl, $_POST[$key]);
	${$key} = str_replace($serc, $repl, $_POST[$key]);
}

$timeparts = explode(" ",microtime());
$starttime = $timeparts[1].substr($timeparts[0],1);

// luodaan tietokantayhteys
require("salasanat.php");

$link = mysql_connect ($dbhost, $dbuser, $dbpass) or die ("Ongelma tietokantapalvelimessa $dbhost");
mysql_select_db ($dbkanta) or die ("Tietokantaa $dbkanta löydy palvelimelta!");

// jos käytetään jotain kummallista porttia (ei http eikä https)
$portti = "";

if ($_SERVER['SERVER_PORT'] != '80' and $_SERVER['SERVER_PORT'] != '443') {
	$portti = ":".$_SERVER['SERVER_PORT'];
}

// otetaan palvelin muuttujasta mahdolliset turhat alut pois
$alut    = array("http://","https://");
$apunimi = str_replace($alut, "", $palvelin);

$tark    = $_SERVER['SERVER_NAME'].$portti.$_SERVER['SCRIPT_NAME'];
$phpnimi = str_replace($apunimi, "", $tark);

if (isset($_COOKIE["matchrace_session"])) {
	$session = $_COOKIE["matchrace_session"];
}
else {
	$session = "";
}

$palvelin2 = "http://$apunimi";

//Onko sittenkin https käytössä
if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == 'on') {
	$palvelin2 = "https://$apunimi";
}

//haetaan kieli
$kalenteri_kieli = $_COOKIE["matchrace_kieli"];

// Initioidaan pari muuttujaa
if (!isset($login)) $login = "";

if ($session == '' and $login != 'yes') {
	require("index.php");
	exit;
}

// jos meillä on sessio
if ($session != '') {
	$query = "	SELECT *
				FROM kuka
				WHERE session = '$session'";
	$result = mysql_query($query) or die ("Kysely ei onnistu kuka $query");

	if (mysql_num_rows($result) != 1 or strlen($session) != 25) {

		$bool = setcookie("matchrace_session", "", time()-432000);

		if ($bool === FALSE) {
			echo "Your browser does not seem to support cookies.";
		}
		else {
			echo "<script>setTimeout(\"parent.location.href='$palvelin2'\",0);</script>";
		}
		exit;
	}

	$kukarow = mysql_fetch_assoc($result);

	$kalet = explode(",", $kukarow["kalenterit"]);
	$KUKA_KALENTERIT = array();

	for ($k = 0; $k < count($kalet); $k++) {
		$KUKA_KALENTERIT[$kalet[$k]] = $kalet[$k];
	}
	
	if (isset($kalenteri_select)) {
		if (isset($KUKA_KALENTERIT[$kalenteri_select])) {
			$query = "	UPDATE kuka
						SET aktiivinen_kalenteri = '$kalenteri_select'
						WHERE kuka = '{$kukarow["kuka"]}'";
			$result = mysql_query($query) or die ("Kysely ei onnistu kuka $query");	
			
			$kukarow["aktiivinen_kalenteri"] = $kalenteri_select;
		}
	}

	// luodaan tietokantayhteys uudelleen, niin saadaan slave käyttöön jos sellaista tarvitaan
	require("connect.inc");

	if ($phpnimi != 'index.php') {

		header("Content-Type: text/html; charset=iso-8859-1");
		
		// otetaan oletukseksi tämä kuukausi ja tämä vuosi
		if ($month == '') 	$month = date("n");
		if ($year == '')  	$year  = date("Y");
		if ($day == '') 	$day   = date("j");

		//lasketaan edellinen ja seuraava paiva kun siirytään yksi päivä
		$backmday = date("n",mktime(0, 0, 0, $month, $day-1,  $year));
		$backyday = date("Y",mktime(0, 0, 0, $month, $day-1,  $year));
		$backdday = date("j",mktime(0, 0, 0, $month, $day-1,  $year));

		$nextmday = date("n",mktime(0, 0, 0, $month, $day+1,  $year));
		$nextyday = date("Y",mktime(0, 0, 0, $month, $day+1,  $year));
		$nextdday = date("j",mktime(0, 0, 0, $month, $day+1,  $year));

		//lasketaan edellinen ja seuraava paiva kun siirytään yksi kuukausi
		$backmmonth = date("n",mktime(0, 0, 0, $month-1, $day,  $year));
		$backymonth = date("Y",mktime(0, 0, 0, $month-1, $day,  $year));
		$backdmonth = date("j",mktime(0, 0, 0, $month-1, $day,  $year));

		$nextmmonth = date("n",mktime(0, 0, 0, $month+1, $day,  $year));
		$nextymonth = date("Y",mktime(0, 0, 0, $month+1, $day,  $year));
		$nextdmonth = date("j",mktime(0, 0, 0, $month+1, $day,  $year));
		
		require("functions.inc");

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
								<td nowrap>
								<div id=\"topmenu_container\">
		    					<ul class=\"menu\" id=\"mainmenu\">
								<li class=\"item1\"><a href=\"{$palvelin2}main.php\"><span>".t("Kalenteri")."</span></a></li>";

		if ($kukarow["superuser"] == 'SUPER' and $kukarow["access"] < 20) {
			echo "<li class=\"item2\"><a href='register.php?paivam=$year-$month-1'><span>".t("Uusi käyttäjä")."</span></a></li>";
		}

		if ($kukarow["access"] < 20) {
			echo "	<li class=\"item3\"><a href='updateacc.php?paivam=$year-$month-1'><span>".t("Päivitä tietosi")."</span></a></li>
				   	<li class=\"item4\"><a href='listaa.php?paivam=$year-$month-1'><span>".t("Listaa purjehtijat")."</span></a></li>
			   		<li class=\"item5\"><a href='scrap.php?paivam=$year-$month-1'><span>".t("Keskustele")."</span></a></li>
					<li class=\"item6\"><a href='listaavaraukset.php?paivam=$year-$month-1'><span>".t("Omat varaukset")."</span></a></li>";
		}

		echo "<li class=\"item11\"><a href='logout.php'><span>".t("Kirjaudu ulos")."</span></a></li>";

		echo "						</ul>
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


		echo "<h3>MatchRace / 606 / SM40 - ".t("Varauskalenterit")."</h3>";

		echo "<form method='post'><h3>".t("Valitse kalenteri").":
				<select name='kalenteri_select' onchange='submit();'>";

		foreach ($KALENTERIT_ARRAY as $kindex => $knimi) {
			if (isset($KUKA_KALENTERIT[$kindex])) {
				$chk = "";
				if ($kukarow["aktiivinen_kalenteri"] == $kindex) $chk = "SELECTED";
				echo "<option value='$kindex' $chk>$knimi</option>";
			}
		}

		echo "	</select>
				</h3>
				</form>";

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
				
		if (isset($paivam)) $xpvm = explode("-",$paivam);
		
	}

	/*
	$query = "	select vene, pinnamies, paivam, pass, count(*), min(id) mintunnus, group_concat(id) tunnukset
				from varaukset
				group by vene, pinnamies, paivam, pass
				having count(*) != 1";
	$vres = mysql_query($query) or die($query);

	while($vrow = mysql_fetch_array($vres)) {
		$query = "	DELETE
					FROM varaukset
					WHERE id in ($vrow[tunnukset]) and id != '$vrow[mintunnus]'";
		$res = mysql_query($query) or die($query);
	}
	*/
}
?>
