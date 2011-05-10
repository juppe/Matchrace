<?php

	// Tämä funktio kääntää sanoja sanakirjasta
	if (!function_exists("t")) {
		function t($stringi, $kieli = "") {
			// tarvitaan yhtiörowta, kukarowta ja tieto slaven:n käytöstä
			global $yhtiorow, $kukarow, $link, $kalenteri_kieli;

			if (!isset($link)) {
				require("salasanat.php");
				$link = mysql_connect ($dbhost, $dbuser, $dbpass) or die ("Ongelma tietokantapalvelimessa $dbhost");
				mysql_select_db ($dbkanta,$link) or die ("Tietokantaa $dbkanta löydy palvelimelta (functions.inc 1)!");
			}

			if (trim($kalenteri_kieli) != '') {
				$indeksi = trim(strtolower($kalenteri_kieli));
			}
			elseif (isset($kukarow["kieli"]) and trim($kukarow["kieli"]) != '') {
				$indeksi = trim(strtolower($kukarow["kieli"]));
			}
			else {
				$indeksi = "fi";
			}
			
			if ($indeksi != 'fi') {
				$sanakirjaquery  = "SELECT fi, $indeksi, tunnus FROM sanakirja WHERE fi = BINARY '$stringi'";
				$sanakirjaresult = mysql_query($sanakirjaquery) or die ("$sanakirjaquery<br><br>".mysql_error());

				if (isset($kaannetyt_sanat) and !in_array($stringi, $kaannetyt_sanat)) $kaannetyt_sanat[] = $stringi;

				if (mysql_num_rows($sanakirjaresult) > 0) {
					$sanakirjarow = mysql_fetch_assoc($sanakirjaresult);

					if (isset($sanakirjarow[$indeksi]) and trim($sanakirjarow[$indeksi]) != '') {
						$stringi = $sanakirjarow[$indeksi];
					}
					else {
						if ($indeksi == 'ru') {
							$stringi = t($stringi,"EN");
						}
						else {
							$stringi = $sanakirjarow["fi"];
						}
					}
				}
				else {
					$sanakirjaquery  = "INSERT INTO sanakirja SET fi = '$stringi', aikaleima=now(), kysytty=1, laatija='$kukarow[kuka]', luontiaika=now()";
					$sanakirjaresult = mysql_query($sanakirjaquery) or die ("$sanakirjaquery<br><br>".mysql_error());
				}
			}

			//	Palautetaan muotoiltu stringi!!!
			if (func_num_args() > 2) {
			    $arg = func_get_args();
				return sprintf($stringi, $arg[2],$arg[3],$arg[4],$arg[5],$arg[6]);
			}
			else {
				return $stringi;
			}
		}
	}


	if (!function_exists("tv1dateconv")) {
		function tv1dateconv($date) {
			list($vv, $kk, $pp) = explode("-", $date);
			return ($pp*1).".".($kk*1).".".$vv;
		}
	}

	//kalenteritoiminnot
	function days_in_month($month, $year) {
		// calculate number of days in a month
		return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
	}

	function weekday_number($day, $month, $year) {
		// calculate weekday number
		$nro = date("w", mktime(0, 0, 0, $month, $day, $year));
		if ($nro==0) $nro=6;
		else $nro--;

		return $nro;
	}
	
?>