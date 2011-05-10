<?php

	// Parametrit
	require('inc/parametrit.inc');

	$xpvm = explode("-", $paivam);

	// Varattava p‰iv‰
	$tpk = date("Ymd", mktime(0, 0, 0, $xpvm[1], $xpvm[2], $xpvm[0]));

	// T‰m‰ paiv‰ ajassa
	$tpa = date("Ymd", mktime(0, 0, 0, date("m"), date("d"), date("Y")));

	// $max_aika_tulevaisuuteen p‰iv‰n p‰iv‰m‰‰r‰
	$tpm = date("Ymd", mktime(0, 0, 0, date("m"), date("d") + $max_aika_tulevaisuuteen[$kukarow["aktiivinen_kalenteri"]], date("Y")));

	//Max varaukset per viikko
	$query = "	SELECT id
	          	FROM varaukset
	          	WHERE paivam >= now()
				and pinnamies = '$kukarow[tunnus]'
				and week(paivam, 1) = week('$paivam', 1)
				AND kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'";
	$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

	if (mysql_num_rows($result) >= $max_varaukset_per_viikko[$kukarow["aktiivinen_kalenteri"]] and $kukarow["superuser"] != 'SUPER') {
		echo "<font class='error'>".t("Sinulla voi olla korkeintaan %s varausta per viikko", "", $max_varaukset_per_viikko[$kukarow["aktiivinen_kalenteri"]])."!</font><br>";
		$tee = 'OHITA';
	}

	//Max tulevat varaukset
	$query = "	SELECT id
	          	FROM varaukset
	          	WHERE paivam >= now()
				and pinnamies ='$kukarow[tunnus]'
				AND kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'";
	$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

	if (mysql_num_rows($result) >= $max_tulevat_varaukset[$kukarow["aktiivinen_kalenteri"]] and $kukarow["superuser"] != 'SUPER') {
		echo "<font class='error'>".t("Sinulla voi olla korkeintaan %s tulevaa varausta", "", $max_tulevat_varaukset[$kukarow["aktiivinen_kalenteri"]])."!</font><br>";
		$tee = 'OHITA';
	}

	//Kuinka pitk‰lle tulavaisuuteen voi varata
	if ($tpk > $tpm and $kukarow["superuser"] != 'SUPER')  {
		echo "<font class='error'>".t("N‰in pitk‰lle tulevaisuuteen ei voi viel‰ varata. Varauksia voi tehd‰ korkeintaan %s p‰iv‰‰ tulevaisuuteen.", "", $max_aika_tulevaisuuteen[$kukarow["aktiivinen_kalenteri"]])."!</font><br>";
		$tee = 'OHITA';
	}

	//Menneisyyteen ei voi varata
	if ($tpk < $tpa)  {
		echo "<font class='error'>".t("Menneisyyteen ei voi tehd‰ varauksia")."!</font><br>";
		$tee = 'OHITA';
	}

	if ($tee == 'UV') {
		echo "<table class='main'>";
    	echo "<form method='post'>";
		echo "<input type='hidden' name='tee' 		value='UV2'>";
        echo "<input type='hidden' name='paivam' 	value='$paivam'>";
		echo "<input type='hidden' name='toiminto' 	value='$toiminto'>";
    	echo "<input type='hidden' name='varausid' 	value='$varausid'>";
    	echo "<input type='hidden' name='pass' 		value='$pass'>";
		echo "	<tr><th align='left'>".t("Varataan k‰ytt‰j‰lle").":</th><td>$kukarow[nimi]</td></tr>
				<tr><th align='left'>".t("P‰iv‰m‰‰r‰").":</th><td>".tv1dateconv($paivam)."</td></tr>
				<tr><th align='left'>".t("Alias").":</th><td>$kukarow[pinnamies]</td></tr>
				<tr><th align='left'>".t("Valittu aika").":</th><td>{$AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]][$pass]}</td></tr>";

		$query = "	SELECT count(*) kpl, max(vene)+1 vene
                    FROM varaukset
					JOIN kuka ON varaukset.pinnamies=kuka.tunnus
                    WHERE varaukset.paivam = '$paivam'
					AND varaukset.pass ='$pass'
					AND varaukset.kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'
                    ORDER BY varaukset.id";
        $result = mysql_query($query) or die ("$query<br><br>".mysql_error());
		$crow = mysql_fetch_array($result);

		if ($crow["kpl"] >= count($VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]])) {
			echo "<tr><th align='left'>".t("HUOM").":</th><td><font class='error'>".t("Kaikki veneet ovat varattuja")."!!!<br>".t("Tarjolla on jononumero").": ".(($crow["kpl"] - count($VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]]))+1)."</font></td></tr>";
		}

		echo "<input type='hidden' name='vene' 	value='$crow[vene]'>";

		$query = "	SELECT kommentit, kommentit2
                	FROM varaukset
                    WHERE id = '$varausid'
					AND kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'";
		$result = mysql_query($query) or die ("$query<br><br>".mysql_error());
		$rrow = mysql_fetch_row($result);

		echo "<tr><th align='left'>".t("Julkinen kommentti").": </th>
				<td width='300'><textarea name='kommentit' rows='2' cols='30' wrap='hard'>$rrow[kommentit]</textarea></td></tr>";

		echo "<tr><th align='left'>".t("Treenin aihe").": (".t("Ei julkinen").") </th>
				<td width='300'><textarea name='kommentit2' rows='2' cols='30' wrap='hard'>$rrow[kommentit2]</textarea></td></tr>";

		echo "<tr><th></th><td><input type='submit' value='".t("Varaa")."'></td></tr></form>";
		echo "</form></table>";
	}

	if ($tee == 'UV2') {
		// Tarkistetaan ettei t‰m‰ varaus ole jo kannassa
		$query = "	SELECT varaukset.vene, varaukset.pinnamies, kuka.pinnamies, kuka.nimi
                    FROM varaukset
					JOIN kuka ON varaukset.pinnamies=kuka.tunnus
                    WHERE varaukset.paivam = '$paivam'
					and varaukset.pass	 = '$pass'
					and varaukset.vene	 = '$vene'
					AND varaukset.kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'
                    ORDER BY varaukset.vene";
        $result = mysql_query($query) or die ("$query<br><br>".mysql_error());

		if (mysql_num_rows($result) == 0) {
    		$query = "	INSERT into varaukset
				 		SET vene	= '$vene',
		    	 		pass		= '$pass',
		     			paivam		= '$paivam',
		     			kommentit	= '$kommentit',
		     			kommentit2	= '$kommentit2',
		     			pinnamies	= '$kukarow[tunnus]',
						kalenteri 	= '{$kukarow["aktiivinen_kalenteri"]}'";
	        $result = mysql_query($query) or die ("$query<br><br>".mysql_error());

			echo t("Lis‰t‰‰n varaus p‰iv‰lle").": ".tv1dateconv($paivam)." ".t("Ajalle").": {$AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]][$pass]}<br>";

			if ($toiminto == 'editoi' and $varausid != '') {

				//Hetaan poistettavan varauksen tiedot
				$query = "	SELECT varaukset.vene, varaukset.pinnamies, kuka.pinnamies kukapinna, kuka.nimi,
							varaukset.kommentit, varaukset.pass, varaukset.paivam
			              	FROM varaukset
			 				JOIN kuka ON varaukset.pinnamies=kuka.tunnus
			                WHERE varaukset.id = '$varausid'
							AND varaukset.kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'";
			    $result = mysql_query($query) or die ("$query<br><br>".mysql_error());
				$srow = mysql_fetch_array($result);

				$query = "	DELETE
							FROM varaukset
					  		WHERE id = '$varausid'
							AND kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'";
				$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

				// Katsotaan nostammeko jonkun varapaikalta varsinaiselle paikalle
				$query = "	SELECT varaukset.vene, kuka.nimi, kuka.eposti, kuka.puhno
		                    FROM varaukset
		 					JOIN kuka ON varaukset.pinnamies=kuka.tunnus
		                    WHERE varaukset.paivam = '$srow[paivam]'
							and varaukset.pass = '$srow[pass]'
							AND varaukset.kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'
		                    ORDER BY id";
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
			}

			echo "<META HTTP-EQUIV='Refresh'CONTENT='1;URL=main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>";
		}
		else {
			echo "<font class='error'>".t("VIRHE: T‰m‰ varaus on jo tallennettu").": ".tv1dateconv($paivam)." ".t("Ajalle").": ".$AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]][$pass]."</font><br>";
		}
	}

	if ($tee == 'UPAIVA') {
		for($a=0; $a < count($vene); $a++) {
			for($b=0; $b < count($aika); $b++) {
				if ($vene[$a]!='' and $aika[$b]!='' and $paivam!='' and $pmies!='') {
					$query = "	SELECT varaukset.vene, varaukset.pinnamies, kuka.pinnamies, kuka.nimi
			                    FROM varaukset
			 					JOIN kuka ON varaukset.pinnamies=kuka.tunnus
			                    WHERE varaukset.paivam 	= '$paivam'
								and varaukset.pass	 	= '$aika[$b]'
								and varaukset.vene	 	= '$vene[$a]'
								AND varaukset.kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'
			                    ORDER BY varaukset.vene";
			        $result = mysql_query($query) or die ("$query<br><br>".mysql_error());

					if (mysql_num_rows($result) == 0) {
			    		$query = "	INSERT into varaukset
							 		SET vene	= '$vene[$a]',
					    	 		pass		= '$aika[$b]',
					     			paivam		= '$paivam',
					     			kommentit	= '$kommentit',
					     			kommentit2	= '$kommentit2',
					     			pinnamies	= '$pmies',
									kalenteri	= '{$kukarow["aktiivinen_kalenteri"]}'";
				        $result = mysql_query($query) or die ("$query<br><br>".mysql_error());

						echo t("Lis‰t‰‰n varaus p‰iv‰lle").": ".tv1dateconv($paivam)." ".t("Ajalle").": ".$AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]][$aika[$b]]."<br>";
					}
					else {
						echo "<font class='error'>".t("VIRHE: T‰m‰ varaus on jo tallennettu").": ".tv1dateconv($paivam)." ".t("Ajalle").": ".$AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]][$aika[$b]]."</font><br>";
					}
				}
			}
		}

		echo "<META HTTP-EQUIV='Refresh'CONTENT='1;URL=main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>";
	}

	if ($tee == '') {
		if ($a == 'aa') {
			$query = "	SELECT upper(kuka.pinnamies) pinnamies, kuka.tunnus, kuka.nimi, kuka.superuser
                      	FROM varaukset
						LEFT JOIN kuka ON varaukset.pinnamies=kuka.tunnus
                      	WHERE varaukset.id = '$varausid'
						AND varaukset.kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'";
            $result = mysql_query($query) or die ("$query<br><br>".mysql_error());
			$row = mysql_fetch_array($result);
		}
		else{
			$query = "	SELECT upper(kuka.pinnamies) pinnamies, kuka.tunnus, kuka.nimi, kuka.superuser
					  	FROM kuka
					  	WHERE session = '$kukarow[session]'";
            $result = mysql_query($query) or die ("$query<br><br>".mysql_error());
			$row = mysql_fetch_array($result);
		}

		if ($row["tunnus"] != $kukarow["tunnus"] and $kukarow["superuser"] != 'SUPER') {
			echo "<font class='error'>".t("Et voi muuttaa muiden k‰ytt‰jien varauksia")."!</font><br>";
			exit;
		}

		echo "<table class='main'>";
		echo "<form method='post'>";
		echo "<input type='hidden' name='tee' 		value='UV'>";
		echo "<input type='hidden' name='paivam' 	value='$paivam'>";
		echo "<input type='hidden' name='toiminto' 	value='$toiminto'>";
		echo "<input type='hidden' name='varausid' 	value='$varausid'>";
		echo "<tr><th align='left'>".t("Varataan k‰ytt‰j‰lle").":</th><td>$row[nimi]</td></tr>";

		if ($toiminto == 'editoi') {
			$query = "	SELECT paivam, pass
                      	FROM varaukset
                      	WHERE id = '$varausid'
						AND kalenteri = '{$kukarow["aktiivinen_kalenteri"]}'";
           	$result = mysql_query($query) or die ("$query<br><br>".mysql_error());
           	$trow = mysql_fetch_array($result);

			echo "<th align='left'>".t("P‰iv‰m‰‰r‰").":</th><td><select name='paivam'>";

			for($i = -15; $i <= 15; $i++) {

				$erp = substr($trow["paivam"], 8, 2);
				$erk = substr($trow["paivam"], 5, 2);
				$erv = substr($trow["paivam"], 0, 4);

				$valpvm = date("Y-m-d", mktime(0, 0, 0, $erk, $erp+$i, $erv));
				$dd1 = date("w", mktime(0,0,0, $erk, $erp+$i, $erv))-1;

				if ($dd1 == -1) $dd1 =6;

				if ($valpvm == $trow["paivam"]) {
                	$sel = "SELECTED";
				}
				else{
					$sel = "";
				}
				echo "<option value='$valpvm' $sel>".t($DAY_ARRAY[$kukarow["aktiivinen_kalenteri"]][$dd1])." ".tv1dateconv($valpvm)."</option>";
			}
			echo "</select></td>";
		}
		else{
			echo "<tr><th align='left'>".t("P‰iv‰m‰‰r‰").":</th><td>".tv1dateconv($paivam)."</td></tr>";
		}

		echo "<tr><th align='left'>".t("Alias").": </th><td width='300'>$row[0]</td></tr>";
		echo "<tr><th align='left'>".t("Valitse aika").":</th><td><select name='pass'>";

		foreach($AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]] as $a => $aika) {
			if ($a == $trow["pass"]) {
            	$sel = "SELECTED";
			}
			else{
				$sel = "";
			}

			echo "<option value='$a' $sel>$aika</option>";
		}

		echo "</select>";
		echo "</td></tr>";
		echo "<tr><th>&nbsp;</th><td><input type='submit' value='".t("Jatka")."'></td></tr>";
		echo "</form></table>";

		if ($kukarow["superuser"] == 'SUPER' and $toiminto != 'editoi') {

        	echo "<form method='post'>
        	        <input type='hidden' name='tee' value='UPAIVA'>
        	        <input type='hidden' name='paivam' value='$paivam'>";

        	echo "<br><br><table class='main'>";
			echo "<tr><th colspan='3'>Admin: (".t("varaa monta venett‰ kerralla").")</th></tr>";
			echo "<tr><td>".t("Varataan k‰ytt‰j‰lle")."</td><td>".t("Valitse veneet")."</td><td>".t("Valitse ajat")."</td></tr>";
            echo "<tr><td valign='top'><select name='pmies'>";

            $query = "	SELECT pinnamies, tunnus, nimi, superuser
                      	FROM kuka
						WHERE kuka.kalenterit = '{$kukarow["aktiivinen_kalenteri"]}'
						or kuka.kalenterit like '{$kukarow["aktiivinen_kalenteri"]},%'
						or kuka.kalenterit like '%,{$kukarow["aktiivinen_kalenteri"]},%'
						or kuka.kalenterit like '%,{$kukarow["aktiivinen_kalenteri"]}'
						ORDER BY pinnamies";
           	$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

            while ($krow = mysql_fetch_row($result)) {
				echo "<option value='$krow[1]'>$krow[0] ($krow[2])</option>";
			}
			echo "</td><td valign='top'>";

			for ($s=0; $s < count($VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]]); $s++) {
				echo "{$VENE_ARRAY[$kukarow["aktiivinen_kalenteri"]][$s]} <input type='checkbox' name='vene[]' value='$s'><br>";
			}
        	echo "</td><td valign='top'>";
			for($s=0; $s < count($AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]]); $s++) {
				echo "{$AIKA_ARRAY[$kukarow["aktiivinen_kalenteri"]][$s]} <input type='checkbox' name='aika[]' value='$s'><br>";
			}
			echo "</td></tr>";
			echo "<tr><th colspan='3'><input type='submit' value='".t("Varaa")."'></th></tr>";
        	echo "</form></table>";
        }
	}

	require('inc/footer.inc');
?>