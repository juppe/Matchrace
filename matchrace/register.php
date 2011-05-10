<?php

	// Parametrit
	require('inc/parametrit.inc');

	if ($tee == 'LISAAKAYTTAJA') {
		$passwd1  = trim($passwd1);
		$passwd2  = trim($passwd2);
		$usercode = trim($usercode);

        $virhe = "";
		$kaleinsert = "";

		if ($usercode == "") {
			$virhe .= t("VIRHE: Käyttäjätunnus puuttuu")."!<br>";
			$tee = "";
		}
		else {
			$query = "SELECT kuka
	       	          FROM kuka
	       	          WHERE kuka = '$usercode'";
	       	$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

			if (mysql_num_rows($result) > 0) {
				$virhe .= t("VIRHE: Käyttäjätunnus '%s' on jo käytössä", "", $usercode)."!<br>";
				$tee = "";
			}
		}

		if ($pinnamies == "") {
			$virhe .= t("VIRHE: Alias puuttuu")."!<br>";
			$tee = "";
		}
		else {
			$query = "SELECT kuka
	       	          FROM kuka
	       	          WHERE pinnamies = '$pinnamies'";
	       	$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

			if (mysql_num_rows($result) > 0) {
				$virhe .= t("VIRHE: Alias '%s' on jo käytössä", "", $pinnamies)."!<br>";
				$tee = "";
			}
		}

		if ($passwd1 != $passwd2) {
			$virhe .= t("VIRHE: Salasanat eivät täsmää")."!<br>";
			$tee = "";
		}

		if ($usercode == '' or $passwd1 == '' or $passwd2 == '' or $pinnamies == '' or $firname == '') {
			$virhe .= t("VIRHE: Pakollisia tietoja puuttuu")."!<br>";
			$tee = "";
		}

		if (strlen($passwd1) < 5) {
    	    $virhe .= t("VIRHE: Salasana on liian lyhyt! Minimipituus on 5 merkkiä").".<br>";
			$tee = "";
		}

		if (!isset($kalenterit) or count($kalenterit) == 0) {
			$virhe .= t("VIRHE: Valitse kalenterit käyttäjälle")."!<br>";
			$tee = "";
		}
		else {
			$kaleinsert = implode(",", $kalenterit);
		}

    	if ($tee == 'LISAAKAYTTAJA') {
           	$query = "	INSERT into kuka SET
						kalenterit	= '$kaleinsert',
						kuka 		= '$usercode',
	                    salasana 	= '$passwd1',
	                    pinnamies 	= '$pinnamies',
						nimi 		= '$firname',
	                    puhno 		= '$phonenum',
						access 		= '10',
	                    eposti 		= '$email'";
			$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

			echo "<br><br><br>".t("Käyttäjä '%s' luotiin onnistunesti", "", $usercode)."!";
			echo "<META HTTP-EQUIV='Refresh'CONTENT='1;URL=main.php'>";
		}
		else {
			echo "<br>$virhe";
		}

		$tee = "";
	}


	if ($tee == '') {

		echo "<h2>".t("Luo uusi käyttäjä").":</h2>";

		echo "<form method='post'>";
		echo "<input type='hidden' name='tee' value='LISAAKAYTTAJA'>";

        echo "<table class='main'>";
		echo "<tr><th>".t("Käyttäjätunnus")."</th><td><input type='text' size='35' name='usercode' maxlength='10' value='$usercode'></td><td>(".t("pakollinen tieto").")</td></td></tr>";
		echo "<tr><th>".t("Nimi")."</th><td><input type='text' size='35' maxlength='35' name='firname' value='$firname'></td><td>(".t("pakollinen tieto").")</td></td></tr>";
		echo "<tr><th>".t("Kalenterit")."</th><td>";

		foreach ($KALENTERIT_ARRAY as $kindex => $knimi) {
			$chk = "";
			if (isset($kalenterit[$kindex]) and $kalenterit[$kindex] > 0) $chk = "CHECKED";

			echo "<input type='checkbox' name='kalenterit[$kindex]' value='$kindex' $chk> - $knimi<br>";
		}

		echo "</td><td>(".t("pakollinen tieto").")</td></tr>";
        echo "<tr><th>".t("Alias")."</th><td><input type='text' size='35' maxlength='3' name='pinnamies' value='$pinnamies'></td><td>(".t("pakollinen tieto").")</td></td></tr>";
		echo "<tr><th>".t("Puhelin")."</td><td><input type='text' size='35' maxlength='35' name='phonenum' value='$phonenum'></td><td></td></tr>";
        echo "<tr><th>".t("Sähköposti")."</th><td><input type='text' size='35' maxlength='35' name='email' value='$email'></td><td></td></tr>";
		echo "<tr><th>".t("Salasana")."</th><td><input type='password' size='35' maxlength='10' name='passwd1' value='$passwd1'></td><td>(".t("pakollinen tieto").")</td></td></tr>";
        echo "<tr><th>".t("Salasana")." (".t("uudestaan").")</th><td><input type='password' size='35' maxlengt='10' name='passwd2' value='$passwd2'></td><td>(".t("pakollinen tieto").")</td></td></tr>";
        echo "</table>";
		echo "<br><input type='submit' value='".t("Lisää käyttäjä")."'>";
		echo "</form>";
	}

	require('inc/footer.inc');
?>