<?php

	// Parametrit
	require('inc/parametrit.inc');

	$xpvm = explode("-",$paivam);

	echo "<table class='main' align='center'>";
	echo "<tr><td class='back' valign='bottom' nowrap><img src='logo2.gif'></td><td class='back' nowrap><h1>MRC - Varauskalenteri: ". $MONTH_ARRAY[$month] ." $year</h1></td></tr>";
	echo "<tr><td class='back'><a href='main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>Takaisin kalenteriin</a></td></tr>";
	echo "<tr><td class='back'><br></td></tr>";
	echo "<tr><td class='back' colspan='2'>";

	// T‰m‰ paiv‰ ajassa
	$tpa = date("Ymd", mktime(0, 0, 0, date("m"), date("d"), date("Y")));

	// Kellonaika nyt
	$hma = date("Hi");

	//Hetaan varauksen tiedot
	$query = "	SELECT varaukset.vene, varaukset.pinnamies, kuka.pinnamies kukapinna, kuka.nimi,
				varaukset.kommentit, varaukset.pass, varaukset.paivam,
				date_format(date_sub(varaukset.paivam, INTERVAL $max_perumispaiva DAY), '%Y%m%d') tpk, date_sub(varaukset.paivam, INTERVAL $max_perumispaiva DAY) tpk_echo
              	FROM varaukset
 				JOIN kuka ON varaukset.pinnamies=kuka.tunnus
                WHERE varaukset.id = '$varausid'";
    $result = mysql_query ($query) or die ("Query failed $query");
	$srow = mysql_fetch_array($result);

	if ($tee == '') {
       	echo "<table class='main'>";
		echo "<tr><th  align='left'colspan='2'>Varauksen tiedot</th></tr>";
		echo "<tr><th align='left'>Varaus: </th><td>$srow[nimi] ($srow[kukapinna])</td></tr>
		      <tr><th align='left'>P‰iv‰m‰‰r‰: </th><td>".tv1dateconv($srow["paivam"])."</td></tr>
		      <tr><th align='left'>Aika: </th><td>".$AIKA_ARRAY[$srow["pass"]]."</td></tr>
		      <tr><th align='left'>Kommentit: </th><td> $srow[kommentit] &nbsp;</td></tr>";

		if ($kukarow["tunnus"] == $srow["pinnamies"] and ($srow["tpk"] > $tpa or ($srow["tpk"] == $tpa and $hma <= $max_perumisaika)) and $kukarow["access"] < 20) {
			echo "<form method='post' action='$PHP_SELF'>";
			echo "<input type='hidden' name='tee' 		value='editoi'>";
			echo "<input type='hidden' name='paivam' 	value='$paivam'>";
			echo "<input type='hidden' name='varausid' 	value='$varausid'>";
			echo "<tr><th></th><td><input type='submit' name='kumpi' value='Poista'>&nbsp;&nbsp;<input type='submit' name='kumpi' value='Muuta'></td></tr>";
			echo "</form>";
		}
		elseif ($srow["tpk"] < $tpa or ($srow["tpk"] == $tpa and $hma > $max_perumisaika)) {
			echo "<tr><th align='left'>Huom:</th><td><font class='error'>Varaus lukittu: ".tv1dateconv($srow["tpk_echo"])." kello: $max_perumisaika</font></td></tr>";
		}

		echo "</table>";

		if ($kukarow["superuser"] == 'SUPER' and ($srow["tpk"] > $tpa or ($srow["tpk"] == $tpa and $hma <= $max_perumisaika)) and $kukarow["access"] < 20) {
			echo "<br><br><table class='main'>";
			echo "<form method='post' action='$PHP_SELF'>";
			echo "<input type='hidden' name='paivam' 	value='$paivam'>";
            echo "<input type='hidden' name='tee' value='editoi'>";
            echo "<input type='hidden' name='varausid' value='$varausid'>";
            echo "<tr><th align='left' colspan='2'>Admin:</th></tr>";
			echo "<tr><td>Poista t‰m‰ varaus:</td><td><input type='submit' name='kumpi' value='Poista'></td></tr>
				  <tr><td>Muuta t‰t‰ varausta:</td><td><input type='submit' name='kumpi' value='Muuta'></td></tr>
				  <tr><td>Poista kaikki p‰iv‰n varaukset:</td><td><input type='submit' name='kumpi' value='Tyhjenn‰ p‰iv‰'></td></tr>";
          	echo "</table></form>";
		}
	}

	if ($tee == 'editoi') {
		if ($kumpi == 'Poista') {
			if ($srow["tpk"] >= $tpa) {

				$query = "	DELETE
							FROM varaukset
							WHERE id = '$varausid'";
				$result = mysql_query ($query) or die ("Query failed $query");

				echo "<br>Varaus on poistettu!<br>";

				// Katsotaan nostammeko jonkun varapaikalta varsinaiselle paikalle
				$query = "	SELECT varaukset.vene, kuka.nimi, kuka.eposti, kuka.puhno
		                    FROM varaukset
		 					JOIN kuka ON varaukset.pinnamies=kuka.tunnus
		                    WHERE paivam = '$srow[paivam]' and pass = '$srow[pass]'
		                    ORDER BY id";
		        $result = mysql_query($query);

				$lask = 1;

				while ($crow = mysql_fetch_array($result)) {
					if ($lask == count($VENE_ARRAY)) {
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
				echo "<br><br>Vanhoja varauksia ei voi poistaa!<br><br>";
			}
		}
		if ($kumpi == 'Tyhjenn‰ p‰iv‰' and $kukarow["superuser"] == 'SUPER' and $kukarow["access"] < 20) {
			if ($srow["tpk"] >= $tpa) {
				$query = "	DELETE
				       		FROM varaukset
				       		WHERE paivam='$srow[paivam]'";
				$result = mysql_query ($query) or die ("Query failed $query");

				echo "<br><br>P‰iv‰ ".tv1dateconv($paivam)." on tyhjennetty!<br>";
				echo "<META HTTP-EQUIV='Refresh'CONTENT='1;URL=main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>";
				exit;
			}
			else{
				echo "<br><br>Vanhoja varauksia ei voi poistaa!<br><br>";
			}
		}
		if ($kumpi == 'Muuta') {
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
        		echo "<br><br>Vanhoja varauksia ei voi muuttaa!<br><br>";
            }
        }
	}

	echo "</td></tr>";
	echo "<tr><td class='back'><br></td></tr>";
	echo "<tr><td class='back'><a href='main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>Takaisin kalenteriin</a></td></tr>";
	echo "</table>";
	echo "</body></html>";
?>
