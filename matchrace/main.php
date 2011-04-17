<?php

	// Parametrit
	require('inc/parametrit.inc');

	echo "<table class='main' align='center'>";
	echo "<tr><td class='back' valign='bottom' nowrap><img src='logo2.gif'></td><td class='back' nowrap><h1>MRC - Varauskalenteri: ". $MONTH_ARRAY[$month] ." $year</h1></td></tr>";
	
	echo "<tr><td class='back' colspan='2'>";
	echo "<table width='100%'>";
	echo "<form action='$PHP_SELF' method='post'>";
	
	if($kukarow["superuser"] == 'SUPER' and $kukarow["access"] < 20){
		echo "<th><a class='menu' href='register.php?paivam=$year-$month-1'>Uusi käyttäjä</a></th>";
	}
	
	if($kukarow["access"] < 20){
		echo "	<th><a class='menu' href='updateacc.php?paivam=$year-$month-1'>Päivitä tietosi</a></th>
			   	<th><a class='menu' href='listaa.php?paivam=$year-$month-1'>Listaa purjehtijat</a></th>
			   	<th><a class='menu' href='scrap.php?paivam=$year-$month-1'>Keskustele</a></th>
			   	<th><a class='menu' href='listaavaraukset.php?paivam=$year-$month-1'>Listaa omat varaukset</a></th>";
	}
	
	echo "<th><a class='menu' href='logout.php'>Kirjaudu ulos</a></th>";
	echo "<th><a class='menu' href='$PHP_SELF?day=1&month=$backmmonth&year=$backymonth'> << </a></th> ";
	echo "<th><select name='month' Onchange='submit();'>";

	$i=1;   
	foreach($MONTH_ARRAY as $val) {
		if($i == $month) { 
			$sel = "selected"; 
		}
		else { 
			$sel = ""; 
		}
		echo "<option value='$i' $sel>$val</option>";
		$i++;
	}
	
	echo "</select></th>";
	echo "<th><a class='menu' href='$PHP_SELF?day=1&month=$nextmmonth&year=$nextymonth'> >> </a></th>";
	echo "</tr>";
	echo "</form>";
	echo "</table>";
	echo "</td></tr>";
	
	
	echo "<tr><td class='back' colspan='2'>";
	echo "<table width='100%'>";
   	
	echo "<tr>";
	
	for($r=0; $r < sizeof($DAY_ARRAY); $r++) {
		echo "	<th nowrap><b>$DAY_ARRAY[$r]</b>
				<br>
				<table width='100%'>
				<tr>";
				
		for($s=0; $s < sizeof($AIKA_ARRAY); $s++) {
			echo "<td align='center' width='35' nowrap>$AIKA_ARRAY[$s]</td>";
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
	
    for($i = 1; $i <= days_in_month($month, $year); $i++) {
	
		// Tämä päivä kalenterissa
		$tpk = date("Ymd", mktime(0, 0, 0, $month, $i, $year));
		
		// Tämä paivä ajassa
		$tpa = date("Ymd", mktime(0, 0, 0, date("m"), date("d"), date("Y")));
		
		// $max_aika_tulevaisuuteen päivän päivämäärä
		$tpm = date("Ymd", mktime(0, 0, 0, date("m"), date("d") + $max_aika_tulevaisuuteen, date("Y")));
				
		if($kukarow["access"] < 20 and (($tpk >= $tpa and $tpk <= $tpm) or ($kukarow["superuser"] == 'SUPER' and $tpk >= $tpa))) {
			echo "<td class='day'><b>$i</b> <a href='tapahtuma.php?paivam=$year-$month-$i'>Varaa</a><br>";
		}
		else{
			echo "<td class='day'><b>$i</b><br>";
		}

		$query = "	SELECT 
					varaukset.pass, varaukset.id, varaukset.vene, upper(kuka.pinnamies) pinnamies
					FROM varaukset
					LEFT JOIN kuka ON varaukset.pinnamies=kuka.tunnus
					WHERE varaukset.paivam	= '$year-$month-$i'
					ORDER BY varaukset.pass, varaukset.id";
		$vres = mysql_query($query) or die($query);
	
		$varaukset 	= array();
		
		if (mysql_num_rows($vres) > 0) {
			while($vrow = mysql_fetch_array($vres)) {
				for($b = 0; $b < count($AIKA_ARRAY); $b++) {
					if ($vrow["pass"] == $b) {
						$varaukset[$b][] = $vrow["id"]."|||".$vrow["pinnamies"];
					}
				}
			}
		}
		
		$koot = array();
		
		for($b = 0; $b < count($AIKA_ARRAY); $b++) {
			$koot[$b] = count($varaukset[$b]);
		}
		
		echo "<table width='100%'>";
		
		for($a = 0; $a < count($VENE_ARRAY); $a++) {
			echo "<tr>";
			for($b = 0; $b < count($AIKA_ARRAY); $b++) {
				if (isset($varaukset[$b][$a])) {
					list($varausid, $pinnamies) = explode("|||", $varaukset[$b][$a]);
					
					echo "<td align='center' style='width: 35px; height: 15px;'><a class='td' href='edit.php?varausid=$varausid&paivam=$year-$month-$i'>$pinnamies</a></td>";
				}
				else {
                    if ($a >= $koot[$b] and $koot[$b] >= 0) {
						if($kukarow["access"] < 20 and (($tpk >= $tpa and $tpk <= $tpm) or ($kukarow["superuser"] == 'SUPER' and $tpk >= $tpa))) {
			   				echo "<td rowspan='".(count($VENE_ARRAY)-$a)."' class='green' style='width: 35px; height: ".(15*(count($VENE_ARRAY)-$a))."px;'>";
							echo "<a class='td' style='height: ".(15*(count($VENE_ARRAY)-$a))."px;' href='tapahtuma.php?paivam=$year-$month-$i&pass=$b&tee=UV'>&nbsp;</a></td>";
						}
	                    else {
							echo "<td rowspan='".(count($VENE_ARRAY)-$a)."' align='center' style='width: 35px; height: ".(15*(count($VENE_ARRAY)-$a))."px;'>&nbsp;</td>";
	                	}
	
						// Tähän tullaan vain kerran
						$koot[$b] = -1;
					}				
                }				
			}
			echo "</tr>";
		}
		
		rsort($koot);
		
		if ($koot[0] >= count($VENE_ARRAY)) {
			echo "<tr>";
		
			for($b = 0; $b < count($AIKA_ARRAY); $b++) {
				echo "<td align='center' class='red' style='width: 35px; height: 15px;'>";
			
				if ($koot[0] > count($VENE_ARRAY)) {
					for($a = count($VENE_ARRAY); $a < $koot[0]; $a++) {		
			
						if (isset($varaukset[$b][$a])) {
							list($varausid, $pinnamies) = explode("|||", $varaukset[$b][$a]);
					
							echo "<a class='td' href='edit.php?varausid=$varausid&paivam=$year-$month-$i'>$pinnamies</a>";
						}			
					}
				}
				if($kukarow["access"] < 20 and (($tpk >= $tpa and $tpk <= $tpm) or ($kukarow["superuser"] == 'SUPER' and $tpk >= $tpa))) {
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
	echo "</body></html>";
?>
