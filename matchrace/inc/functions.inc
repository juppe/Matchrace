<?php

	// näin kuollaan mysql errorista...
	if (!function_exists("pupe_error")) {
		// otetaan parametriksi query
		function pupe_error($query) {
			// tarvitaan yhtiörowta ja kukarowta
			global $yhtiorow, $kukarow;

			// trimmataan, tabit, rivinvaihdot ja tuplaspacet pois querystä..
			$query = trim($query);
			$query = str_replace("\t", " ",$query);
			$query = str_replace("\n", "", $query);
			$query = str_replace("\r", "", $query);
			$query = ereg_replace("  +", " ", $query);

			// tehdään errorimessage
			$puperror = "File: $_SERVER[PHP_SELF]\nUser: $kukarow[kuka]\n\n$query\n\n".mysql_error();

			// lähetetään se meilitse adminille
			mail($yhtiorow['admin_email'], $yhtiorow['nimi']." - SQL Error", $puperror, "From: $yhtiorow[postittaja_email]\r\n", "-f $yhtiorow[postittaja_email]");

			// kuollaan pois
			exit("Kysely ei onnistunut tiedostossa $_SERVER[PHP_SELF].<br><br>$query<br><br>".mysql_error());
		}
	}

	if (!function_exists("t")) {
		function t($stringi, $kieli = "") {

			// tarvitaan yhtiörowta, kukarowta ja tieto slaven:n käytöstä
			global $yhtiorow, $kukarow, $link;


			if (trim($kieli) != '') {
				$indeksi = trim(strtolower($kieli));
			}
			elseif (trim($kukarow["kieli"]) != '') {
				$indeksi = trim(strtolower($kukarow["kieli"]));
			}
			else {
				$indeksi = "fi";
			}
			/*
			if ($indeksi != 'fi') {
				$sanakirjaquery  = "SELECT fi, $indeksi FROM sanakirja WHERE fi = BINARY '$stringi'";
				$sanakirjaresult = mysql_query($sanakirjaquery, $link) or pupe_error($sanakirjaquery);

				if (mysql_num_rows($sanakirjaresult) > 0) {
					$sanakirjarow = mysql_fetch_array($sanakirjaresult);
				
					if (isset($sanakirjarow[$indeksi]) and trim($sanakirjarow[$indeksi]) != '') {
						$stringi = $sanakirjarow[$indeksi];
					}
					else {
						$stringi = $sanakirjarow["fi"];
					}
				}
				else {
					$sanakirjaquery  = "INSERT INTO sanakirja SET fi = '$stringi', aikaleima=now(), kysytty=1";
					$sanakirjaresult = mysql_query($sanakirjaquery, $link) or pupe_error($sanakirjaquery);
				}
			}
			*/
			return $stringi;
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

	function weekday_name($day, $month, $year) {
		// calculate weekday name
		$days = array("Maanantai", "Tiistai", "Keskiviikko", "Torstai", "Perjantai", "Lauantai","Sunnuntai");
		$nro = date("w", mktime(0, 0, 0, $month, $day, $year));
		if ($nro==0) $nro=6;
		else $nro--;

		return $days[$nro];
	}

	function weekday_number($day, $month, $year) {
		// calculate weekday number
		$nro = date("w", mktime(0, 0, 0, $month, $day, $year));
		if ($nro==0) $nro=6;
		else $nro--;

		return $nro;
	}

	function month_name($month) {
		// display long month name
		$kk = $MONTH_ARRAY;
		return $kk[$month-1];
	}


?>