<?php

	// Parametrit
	require('inc/parametrit.inc');

	if ($tee == 'PAIVITA_KAYTTAJA') {

		if ($kukarow["superuser"] == 'SUPER' and $edi != '') {
			$muutakuka = $edi;
		}
		else {
			$muutakuka = $kukarow["kuka"];
		}

		$virhe = "";
		$kaleinsert = "";

		if (strlen(trim($passwd1)) > 0) {
			if ($passwd1 != $passwd2) {
				$virhe .= t("VIRHE: Salasanat eivät täsmää")."!<br>";
				$tee = "";
			}

			if (strlen($passwd1) < 5) {
	    	    $virhe .= t("VIRHE: Salasana on liian lyhyt! Minimipituus on 5 merkkiä").".<br>";
				$tee = "";
			}
		}

		if ($firname == '') {
			$virhe .= t("VIRHE: Nimi puuttuu")."!<br>";
			$tee = "";
		}

		if ($kukarow["superuser"] == 'SUPER') {
			if ($pinnamies == "") {
				$virhe .= t("VIRHE: Alias puuttuu")."!<br>";
				$tee = "";
			}
			else {
				$query = "	SELECT kuka
		       	          	FROM kuka
		       	          	WHERE pinnamies = '$pinnamies'
							AND kuka != '$muutakuka'";
		       	$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

				if (mysql_num_rows($result) > 0) {
					$virhe .= t("VIRHE: Alias '%s' on jo käytössä", "", $pinnamies)."!<br>";
					$tee = "";
				}
			}
		}

		if (!isset($kalenterit) or count($kalenterit) == 0) {
			$virhe .= t("VIRHE: Valitse kalenterit käyttäjälle")."!<br>";
			$tee = "";
		}
		else {
			$kaleinsert = implode(",", $kalenterit);
		}

		if ($tee == 'PAIVITA_KAYTTAJA') {
			$query = "UPDATE kuka
					  SET nimi	= '$firname',
					  puhno		= '$phonenum',
					  eposti	= '$email'";

			if ($kukarow["superuser"] == 'SUPER') {
				$query .= " , kalenterit	= '$kaleinsert' ";
				$query .= " , pinnamies		= '$pinnamies' ";
			}

			$query .= "WHERE kuka = '$muutakuka'";
			$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

			if (strlen(trim($passwd1)) > 0) {
				$query = "UPDATE kuka
						  SET salasana = '$passwd1'
						  WHERE kuka = '$muutakuka'";
				$result = mysql_query($query) or die ("$query<br><br>".mysql_error());
			}

			echo "<br><br><br>".t("Käyttäjätietosi päivitettiin onnistuneesti")."!";
			echo "<META HTTP-EQUIV='Refresh'CONTENT='1;URL=main.php'>";
		}
		else {
			echo "<br>$virhe";
		}

		$tee = '';
	}

	if ($tee == '') {

		if ($kukarow["superuser"] == 'SUPER' and $edi != '') {
			$muutakuka = $edi;
		}
		else {
			$muutakuka = $kukarow["kuka"];
		}

		$query = "	SELECT kalenterit, kuka, nimi, pinnamies, puhno, eposti
					FROM kuka
					WHERE kuka = '$muutakuka'";
		$result = mysql_query($query) or die ("$query<br><br>".mysql_error());
		$row = mysql_fetch_assoc($result);

		if (!isset($kalenterit)) {
			$kalet = explode(",", $row["kalenterit"]);
			$kalenterit = array();

			for ($k = 0; $k < count($kalet); $k++) {
				$kalenterit[$kalet[$k]] = $kalet[$k];
			}
		}

		if (!isset($firname)) 		$firname = $row["nimi"];
		if (!isset($pinnamies)) 	$pinnamies = $row["pinnamies"];
		if (!isset($phonenum)) 		$phonenum = $row["puhno"];
		if (!isset($email)) 		$email = $row["eposti"];

		echo "<h2>".t("Päivitä käyttäjätiedot").":</h2>";

		echo "<form method='post'>";
		echo "<input type='hidden' name='tee' value='PAIVITA_KAYTTAJA'>";

		echo "<table class='main'>";
		echo "<tr><th>".t("Käyttäjätunnus")."</th><td>$muutakuka</td></tr>";
		echo "<tr><th>".t("Nimi")."</th><td><input type='text' size='35' value='$firname' maxlenght='25' name='firname'></td></tr>";
		echo "<tr><th>".t("Kalenterit")."</th><td>";

		if ($kukarow["superuser"] == 'SUPER') {
			foreach ($KALENTERIT_ARRAY as $kindex => $knimi) {
				$chk = "";
				if (isset($kalenterit[$kindex]) and $kalenterit[$kindex] > 0) $chk = "CHECKED";

				echo "<input type='checkbox' name='kalenterit[$kindex]' value='$kindex' $chk> - $knimi<br>";
			}
		}
		else {
			foreach ($KALENTERIT_ARRAY as $kindex => $knimi) {
				if (isset($kalenterit[$kindex]) and $kalenterit[$kindex] > 0) {
					echo "$knimi<br>";
				}
			}
		}

		echo "</td></tr>";

		if ($kukarow["superuser"] == 'SUPER') {
			echo "<tr><th>".t("Alias")."</th><td height='25'><input type='text' size='35' value='$pinnamies' maxlenght='3' name='pinnamies'></td></tr>";
		}
		else {
			echo "<tr><th>".t("Alias")."</th><td height='25'>$row[pinnamies]</td></tr>";
		}

		echo "<tr><th>".t("Puhelin")."</th><td><input type='text' size='35' value='$phonenum' maxlenght='25' name='phonenum'></td></tr>";
		echo "<tr><th>".t("Sähköposti")."</th><td><input type='text' size='35' value='$email' maxlenght='25' name='email'></td></tr>";

		echo "<tr><td colspan='2'><br>".t("Jos haluat vaihtaa salasanasi, syötä uusi salasana kahteen kertaan").":</td></tr>";
		echo "<tr><th>".t("Uusi salasana")."</th><td><input type='password' name='passwd1' size='15' maxlength='30'></td></tr>";
		echo "<tr><th>".t("Uusi salasana")."</th><td><input type='password' name='passwd2' size='15' maxlength='30'></td></tr>";

		echo "</table>";
		echo "<br><input type='submit' value='".t("Päivitä tiedot")."'>";
		echo "</form>";

	}

	require('inc/footer.inc');
?>