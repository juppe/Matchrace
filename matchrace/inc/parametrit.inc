<?php

// poistetaan hipsuja ja trimmataan kaikki get muuttujat!
$serc = array("\"","'","<",">","\\", ";");
$repl = array("", "", "", "", "", "");

foreach ($_GET as $key => $value) {
	//$_GET[$key] = addslashes(trim($_GET[$key]));
	$_GET[$key] = str_replace($serc, $repl, $_GET[$key]);
	${$key} = str_replace($serc, $repl, $_GET[$key]);
}

// poistetaan hipsuja ja trimmataan kaikki post muuttujat!
foreach ($_POST as $key => $value) {
	//$_POST[$key] = addslashes(trim($_POST[$key]));
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

if (isset($_COOKIE["pupesoft_session"])) {
	$session = $_COOKIE["pupesoft_session"];
}
else {
	$session = "";
}

$palvelin2 = "http://$apunimi";

//Onko sittenkin https käytössä
if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == 'on') {
	$palvelin2 = "https://$apunimi";
}

//haetaan browserin oletuskieli
$browkieli = substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2);

if ($browkieli == 'sv') {
	$browkieli = 'se';
}
elseif (isset($kieli) and $kieli == 'fi') {
	$browkieli = 'fi';
}
else {
	$browkieli = 'en';
}

//Initioidaan pari muuttujaa
if(!isset($login))		$login = "";


// jos saamme nämä muuttujat getissä, kokeillaan logata niillä sisään
if (isset($_GET["user"]) and $_GET["user"] != "" and $_GET["pass"] != "" and $_GET["yhtio"] != "") {

	$query = "select * from kuka where yhtio='$_GET[yhtio]' and kuka='$_GET[user]' and salasana=md5('$_GET[pass]')";
	$result = mysql_query($query) or die ("Sisäänkirjaus epäonnistui!");

	if (mysql_num_rows($result) == 1) {

		$apukukarow = mysql_fetch_array($result);

		// jos meillä on sessio niin poistetaan se tietokannasta jo aluks
		if ($session != "") {
			$query = "	UPDATE kuka
						SET session = ''
						WHERE session = '$session'";
			$result = mysql_query($query) or die ("Päivitys epäonnistui sisäänkirjautumisessa");
		}

		$session = "";
		for ($i = 0; $i < 25; $i++) {
			$session .= chr(rand(65,90)) ;
		}

		$query = "	UPDATE kuka
					SET session = '$session',
					lastlogin = now()
					WHERE kuka = '$apukukarow[kuka]'
			 		and yhtio = '$apukukarow[yhtio]'";
		$result = mysql_query($query) or die ("Päivitys epäonnistui sisäänkirjautumisessa");

		$query = "	UPDATE kuka
					SET session = ''
					WHERE kuka = '$apukukarow[kuka]'
			 		and yhtio != '$apukukarow[yhtio]'";
		$result = mysql_query($query) or die ("Päivitys epäonnistui sisäänkirjautumisessa");

		$bool = setcookie("pupesoft_session", $session, time()+43200); // 12 tuntia voimassa

		if ($bool === FALSE) {
			die ("Sisäänkirjaus epäonnistui!");
		}
	}
	else {
		die ("Sisäänkirjaus epäonnistui!");
	}

}
// jos sessionia ei ole ja ei olla loginissa, mennään login ruutuun ja exit...
elseif ($session == '' and $login != 'yes') {
	require("index.php");
	exit;
}

// jos meillä on sessio
if ($session != '') {

	$query = "	SELECT *
				FROM kuka
				WHERE session='$session'";
	$result = mysql_query($query) or die ("Kysely ei onnistu kuka $query");

	if (mysql_num_rows($result) != 1 or strlen($session) != 25) {
		if (isset($nayta_pdf) and $nayta_pdf == 1) {
			echo "Your browser does not seem to support cookies.";
			exit;
		}
		$bool = setcookie("pupesoft_session", "", time()-432000);

		if ($bool === FALSE) {
			echo "Your browser does not seem to support cookies.";
		}
		else {
			echo "<script>setTimeout(\"parent.location.href='$palvelin2'\",0);</script>";
		}
		exit;
	}
	$kukarow = mysql_fetch_array($result);

	// luodaan tietokantayhteys uudelleen, niin saadaan slave käyttöön jos sellaista tarvitaan
	require("connect.inc");

	if ($phpnimi != 'index.php') {

		$charset = "iso-8859-1";

		if ($kukarow["kieli"] == "ru" or $kukarow["kieli"] == "ee") {
			$charset = "utf-8";
		}

		header("Content-Type: text/html; charset=$charset");

		echo "<html>";
		echo "<head>";
		echo "<META Http-Equiv='Cache-Control' Content='no-cache'>";
		echo "<META Http-Equiv='Pragma' Content='no-cache'>";
		echo "<META Http-Equiv='Expires' Content='0'>";
		echo "<meta http-equiv='Content-Type' content='text/html; charset=$charset'>";
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
		echo "<title>Varauskalenteri</title>";
		echo "</head>";
		echo "<body>";
	
		$AIKA_ARRAY = array("10-17","17-22");
		$VENE_ARRAY = array("7","6","5","4","3","2","1");

		//Kuinka kauas tulevaiuuteen voidaan varata (päiviä)
		$max_aika_tulevaisuuteen = 1000;

		//Kuinka monta varausta käyttäjällä voi olla per viikko
		$max_varaukset_per_viikko = 4;

		//Kuinka monta varausta käyttäjällä voi olla kaiken kaikkiaan auki
		$max_tulevat_varaukset = 4;
		
		//Kuinka monta päivää ennen varausta on viimeinen perumispäivä
		$max_perumispaiva = 1;
		
		//Viimeinen perumisaika perumispäivänä
		$max_perumisaika = 1200;

		//kuukaudet ja päivät ja ajat
		$MONTH_ARRAY = array(1=>'Tammikuu','Helmikuu','Maaliskuu','Huhtikuu','Toukokuu','Kesäkuu','Heinäkuu','Elokuu','Syyskuu','Lokakuu','Marraskuu','Joulukuu');
		$DAY_ARRAY = array("Maanantai", "Tiistai", "Keskiviikko", "Torstai", "Perjantai", "Lauantai","Sunnuntai");
		
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
