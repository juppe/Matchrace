<?php

	// Parametrit
	require('inc/parametrit.inc');

	// T‰m‰ paiv‰ ajassa
	$tpa = date("Ymd", mktime(0, 0, 0, date("m"), date("d"), date("Y")));

	// Kellonaika nyt
	$hma = date("Hi");

	//Hetaan varauksen tiedot
	$query = "	SELECT varaukset.vene, varaukset.pinnamies, kuka.pinnamies kukapinna, kuka.nimi,
				varaukset.kommentit, varaukset.pass, varaukset.paivam,
				date_format(date_sub(varaukset.paivam, INTERVAL {$max_perumispaiva[$kukarow["aktiivinen_kalenteri"]]} DAY), '%Y%m%d') tpk,
				date_sub(varaukset.paivam, INTERVAL {$max_perumispaiva[$kukarow["aktiivinen_kalenteri"]]} DAY) tpk_echo
              	FROM varaukset
 				JOIN kuka ON varaukset.pinnamies=kuka.tunnus
                WHERE varaukset.id = '$varausid'
				AND varaukset.kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'";
    $result = mysql_query($query) or die ("$query<br><br>".mysql_error());
	$srow = mysql_fetch_array($result);

	if ($tee == '') {

		echo "<h2>".t("Varauksen tiedot").":</h2>";

       	echo "<table class='main'>";
		echo "<tr><th align='left'>".t("Varaus")."</th><td>$srow[nimi] ($srow[kukapinna])</td></tr>
		      <tr><th align='left'>".t("P‰iv‰m‰‰r‰")."</th><td>".tv1dateconv($srow["paivam"])."</td></tr>
		      <tr><th align='left'>".t("Aika")."</th><td>".$AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]][$srow["pass"]]."</td></tr>
		      <tr><th align='left'>".t("Kommentit")."</th><td> $srow[kommentit] &nbsp;</td></tr>";

		if ($kukarow["tunnus"] == $srow["pinnamies"] and ($srow["tpk"] > $tpa or ($srow["tpk"] == $tpa and $hma <= $max_perumisaika[$kukarow["aktiivinen_kalenteri"]])) and $kukarow["access"] < 20) {
			echo "<form method='post' >";
			echo "<input type='hidden' name='tee' 		value='editoi'>";
			echo "<input type='hidden' name='paivam' 	value='$paivam'>";
			echo "<input type='hidden' name='varausid' 	value='$varausid'>";
			echo "<tr><th></th><td>
				<input type='submit' name='Poista' value='".t("Poista")."'>&nbsp;&nbsp;
				<input type='submit' name='Muuta' value='".t("Muuta")."'></td></tr>";
			echo "</form>";
		}
		elseif ($srow["tpk"] < $tpa or ($srow["tpk"] == $tpa and $hma > $max_perumisaika[$kukarow["aktiivinen_kalenteri"]])) {
			echo "<tr><th align='left'>".t("Huom")."</th><td><font class='error'>".t("Varaus lukittu").": ".tv1dateconv($srow["tpk_echo"])." ".t("kello").": {$max_perumisaika[$kukarow["aktiivinen_kalenteri"]]}</font></td></tr>";
		}

		echo "</table>";

		if ($kukarow["superuser"] == 'SUPER' and $kukarow["access"] < 20) {
			#and ($srow["tpk"] > $tpa or ($srow["tpk"] == $tpa and $hma <= $max_perumisaika[$kukarow["aktiivinen_kalenteri"]]))

			echo "<br><br><table class='main'>";
			echo "<form method='post' >";
			echo "<input type='hidden' name='paivam' 	value='$paivam'>";
            echo "<input type='hidden' name='tee' value='editoi'>";
            echo "<input type='hidden' name='varausid' value='$varausid'>";
            echo "<tr><th align='left' colspan='2'>Admin:</th></tr>";
			echo "<tr><td>".t("Poista t‰m‰ varaus")."</td><td><input type='submit' name='Poista' value='".t("Poista")."'></td></tr>
				  <tr><td>".t("Muuta t‰t‰ varausta")."</td><td><input type='submit' name='Muuta' value='".t("Muuta")."'></td></tr>
				  <tr><td>".t("Poista kaikki p‰iv‰n varaukset")."</td><td><input type='submit' name='Tyhjennapaiva' value='".t("Tyhjenn‰ p‰iv‰")."'></td></tr>";
          	echo "</table></form>";
		}
	}

	if ($tee == 'editoi') {
		if (isset($Poista)) {
			if ($srow["tpk"] >= $tpa) {

				$query = "	DELETE
							FROM varaukset
							WHERE id = '$varausid'
							AND kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'";
				$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

				echo "<br>".t("Varaus poistettu")."!<br>";

				// Katsotaan nostammeko jonkun varapaikalta varsinaiselle paikalle
				$query = "	SELECT varaukset.vene, kuka.nimi, kuka.eposti, kuka.puhno
		                    FROM varaukset
		 					JOIN kuka ON varaukset.pinnamies=kuka.tunnus
		                    WHERE varaukset.paivam = '$srow[paivam]'
							and varaukset.pass = '$srow[pass]'
							AND varaukset.kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'
		                    ORDER BY varaukset.id";
		        $result = mysql_query($query) or die ("$query<br><br>".mysql_error());

				$lask = 1;

				while ($crow = mysql_fetch_array($result)) {
					if ($lask == count($VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]])) {
						//L‰hetet‰‰n maili ja tekstari jonopaikalta mukaan p‰‰sseelle
						if ($crow["eposti"] != '') {
							$meili = "\n\n\n$srow[nimi] perui varauksensa ".tv1dateconv($srow["paivam"]).".\n";
							$meili .= "MRC Varauskalenteri kaipaa huomiotasi!\n";

							$tulos = mail($crow["eposti"], "Varauskalenteri", $meili, "From: MRC <matchracing@njk.fi>\nReply-To: MRC <matchracing@njk.fi>\n", "-f matchracing@njk.fi");
						}
						if ($crow["puhno"] != '') {

							$crow["puhno"] 	= urlencode($crow["puhno"]);

							$viesti  = "$srow[nimi] perui varauksensa ".tv1dateconv($srow["paivam"]).".\n";
							$viesti .= "MRC Varauskalenteri kaipaa huomiotasi!\n";
							$viesti  = urlencode($viesti);

							ob_start();
							$retval = @readfile("https://10.1.1.1/sms.php?numero=$crow[puhno]&viesti=$viesti&user=match&pass=race");

							if (false !== $retval) {
								$retval = ob_get_contents();
							}
							ob_end_clean();
						}
					}
					$lask++;
				}

				echo "<META HTTP-EQUIV='Refresh'CONTENT='1;URL=main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>";
				exit;
			}
			else{
				echo "<br><br>".t("Vanhoja varauksia ei voi poistaa")."!<br><br>";
			}
		}

		if (isset($Tyhjennapaiva) and $kukarow["superuser"] == 'SUPER' and $kukarow["access"] < 20) {
			if ($srow["tpk"] >= $tpa) {
				$query = "	DELETE
				       		FROM varaukset
				       		WHERE paivam = '$srow[paivam]'
							AND kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'";
				$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

				echo "<br><br>".t("P‰iv‰ %s on tyhjennetty", "", tv1dateconv($paivam))."!<br>";
				echo "<META HTTP-EQUIV='Refresh'CONTENT='1;URL=main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>";
				exit;
			}
			else{
				echo "<br><br>".t("Vanhoja varauksia ei voi poistaa")."!<br><br>";
			}
		}
		
		if (isset($Muuta)) {
            if ($srow["tpk"] >= $tpa) {
				if ($kukarow["superuser"] == 'SUPER' and $kukarow["access"] < 20) {
					echo "<META HTTP-EQUIV='Refresh'CONTENT='0;URL=tapahtuma.php?paivam=$paivam&a=aa&toiminto=editoi&varausid=$varausid'>";
					exit;
				}
				else{
					echo "<META HTTP-EQUIV='Refresh'CONTENT='0;URL=tapahtuma.php?paivam=$paivam&toiminto=editoi&varausid=$varausid'>";
					exit;
				}
			}
			else{
        		echo "<br><br>".t("Vanhoja varauksia ei voi muuttaa")."!<br><br>";
            }
        }
	}

	require('inc/footer.inc');
?>