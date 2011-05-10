<?php

	// Parametrit
	require('inc/parametrit.inc');

	if ((int) $kukarow["aktiivinen_kalenteri"] == 0) {
		echo t("Valitse käytettävä kalenteri")."!<br>";
	}
	else {
		echo "<table class='main' align='center'>";
		echo "<tr><td class='back' colspan='2'>";

		echo "<form method='post'>";
		echo "<table class='kalenteri' width='100%'>";
		echo "<tr>";
		echo "<th><a class='menu' href='main.php?day=1&month=$backmmonth&year=$backymonth'> << </a></th> ";
		echo "<th align='center'><select name='month' Onchange='submit();'>";

		$i=1;

		foreach ($MONTH_ARRAY[$kukarow["aktiivinen_kalenteri"]] as $val) {
			if ($i == $month) {
				$sel = "selected";
			}
			else {
				$sel = "";
			}
			echo "<option value='$i' $sel>".t("$val")."</option>";
			$i++;
		}

		echo "</select> - $year</th>";
		echo "<th><a class='menu' href='main.php?day=1&month=$nextmmonth&year=$nextymonth'> >> </a></th>";
		echo "</tr>";
		echo "</table>";
		echo "</form>";
		echo "</td></tr>";

		echo "<tr><td class='back' colspan='2'>";
		echo "<table class='kalenteri' width='100%'>";

		echo "<tr>";

		for ($r=0; $r < count($DAY_ARRAY[$kukarow["aktiivinen_kalenteri"]]); $r++) {
			echo "	<th nowrap><b>".t($DAY_ARRAY[$kukarow["aktiivinen_kalenteri"]][$r])."</b>
					<br>
					<table class='kalenteri' width='100%'>
					<tr>";

			for ($s=0; $s < count($AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]]); $s++) {
				echo "<td align='center' width='35' nowrap>{$AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]][$s]}</td>";
			}
	        echo "	</tr>
					</table>
					</th>";
		}
		echo "</tr>";
		echo "<tr>";

		// Kirjotetaan alkuun tyhjiä soluja
		for ($i = 0; $i < weekday_number("1", $month, $year); $i++) {
			echo "<td>&nbsp;</td>";
		}

	    for ($i = 1; $i <= days_in_month($month, $year); $i++) {

			// Tämä päivä kalenterissa
			$tpk = date("Ymd", mktime(0, 0, 0, $month, $i, $year));

			// Tämä paivä ajassa
			$tpa = date("Ymd", mktime(0, 0, 0, date("m"), date("d"), date("Y")));

			// $max_aika_tulevaisuuteen päivän päivämäärä
			$tpm = date("Ymd", mktime(0, 0, 0, date("m"), date("d") + $max_aika_tulevaisuuteen[$kukarow["aktiivinen_kalenteri"]], date("Y")));

			if ($kukarow["access"] < 20 and (($tpk >= $tpa and $tpk <= $tpm) or ($kukarow["superuser"] == 'SUPER' and $tpk >= $tpa))) {
				echo "<td class='day'><b>$i</b> <a href='tapahtuma.php?paivam=$year-$month-$i'>".t("Varaa")."</a><br>";
			}
			else{
				echo "<td class='day'><b>$i</b><br>";
			}

			$query = "	SELECT varaukset.pass, varaukset.id, varaukset.vene, upper(kuka.pinnamies) pinnamies
						FROM varaukset
						LEFT JOIN kuka ON varaukset.pinnamies=kuka.tunnus
						WHERE varaukset.paivam	= '$year-$month-$i'
						AND varaukset.kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'
						ORDER BY varaukset.pass, varaukset.id";
			$vres = mysql_query($query) or die ("$query<br><br>".mysql_error());

			$varaukset 	= array();

			if (mysql_num_rows($vres) > 0) {
				while ($vrow = mysql_fetch_array($vres)) {
					for ($b = 0; $b < count($AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]]); $b++) {
						if ($vrow["pass"] == $b) {
							$varaukset[$b][] = $vrow["id"]."|||".$vrow["pinnamies"];
						}
					}
				}
			}

			$koot = array();

			for ($b = 0; $b < count($AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]]); $b++) {
				$koot[$b] = count($varaukset[$b]);
			}

			echo "<table class='kalenteri' width='100%'>";

			for($a = 0; $a < count($VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]]); $a++) {
				echo "<tr>";
				for($b = 0; $b < count($AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]]); $b++) {
					if (isset($varaukset[$b][$a])) {
						list($varausid, $pinnamies) = explode("|||", $varaukset[$b][$a]);

						echo "<td align='center' style='width: 35px; height: 15px;'><a class='td' href='edit.php?varausid=$varausid&paivam=$year-$month-$i'>$pinnamies</a></td>";
					}
					else {
	                    if ($a >= $koot[$b] and $koot[$b] >= 0) {
							if ($kukarow["access"] < 20 and (($tpk >= $tpa and $tpk <= $tpm) or ($kukarow["superuser"] == 'SUPER' and $tpk >= $tpa))) {
				   				echo "<td rowspan='".(count($VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]])-$a)."' class='green' style='width: 35px; height: ".(15*(count($VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]])-$a))."px;'>";
								echo "<a class='td' style='height: ".(15*(count($VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]])-$a))."px;' href='tapahtuma.php?paivam=$year-$month-$i&pass=$b&tee=UV'>&nbsp;</a></td>";
							}
		                    else {
								echo "<td rowspan='".(count($VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]])-$a)."' align='center' style='width: 35px; height: ".(15*(count($VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]])-$a))."px;'>&nbsp;</td>";
		                	}

							// Tähän tullaan vain kerran
							$koot[$b] = -1;
						}
	                }
				}
				echo "</tr>";
			}

			rsort($koot);

			if ($koot[0] >= count($VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]])) {
				echo "<tr>";

				for($b = 0; $b < count($AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]]); $b++) {
					echo "<td align='center' class='red' style='width: 35px; height: 15px;'>";

					if ($koot[0] > count($VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]])) {
						for($a = count($VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]]); $a < $koot[0]; $a++) {

							if (isset($varaukset[$b][$a])) {
								list($varausid, $pinnamies) = explode("|||", $varaukset[$b][$a]);

								echo "<a class='td' href='edit.php?varausid=$varausid&paivam=$year-$month-$i'>$pinnamies</a>";
							}
						}
					}
					if ($kukarow["access"] < 20 and (($tpk >= $tpa and $tpk <= $tpm) or ($kukarow["superuser"] == 'SUPER' and $tpk >= $tpa))) {
		   				echo "<a class='td' href='tapahtuma.php?paivam=$year-$month-$i&pass=$b&vene=$a&tee=UV'>&nbsp;</a>";
					}

					echo "</td>";
				}
				echo "</tr>";
			}

			echo "</table>";
			echo "</td>";

			if (weekday_number($i, $month, $year) == 6) {
				// Rivinvaihto jos seuraava viikko on olemassa
				if (days_in_month($month, $year)!=$i) {
					echo "</tr><tr>";
				}
			}
		}

		// Kirjotetaan loppuun tyhjiä soluja
		if (weekday_number($i, $month, $year) <= 6 and weekday_number($i, $month, $year) > 0) {
			for ($a = weekday_number($i, $month, $year); $a <= 6; $a++) {
				echo "<td>&nbsp;</td>";
			}
		}
		echo "</tr>";
		echo "</table>";
		echo "</td></tr></table>";
	}

	require('inc/footer.inc');
?>