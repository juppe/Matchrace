<?php

	if (isset($_POST["tee"])) {
		if($_POST["tee"] == 'lataa_tiedosto') $lataa_tiedosto=1;
		if($_POST["kaunisnimi"] != '') $_POST["kaunisnimi"] = str_replace("/","",$_POST["kaunisnimi"]);
	}

	// Parametrit
	require('inc/parametrit.inc');

	if (isset($tee) and $tee == "lataa_tiedosto") {
		readfile("/tmp/".$tmpfilenimi);
		exit;
	}

	echo "<h2>".t("Varaustilastot").":</h2>";
	
	if ($kukarow["superuser"] == 'SUPER' and $kukarow["access"] < 20) {

		echo "<form method='post'>";
		echo "<table class='main'>";
		echo "<tr>";
		echo "<th>".t("Valitse vuosi").":</th>";
		echo "<th align='center'><select name='vuosi' Onchange='submit();'>";

		if (!isset($vuosi)) $vuosi = date("Y");

		for ($i = 2003; $i <= date('Y'); $i++) {

			$sel = "";

			if ($i == $vuosi) {
				$sel = "selected";
			}

			echo "<option value='$i' $sel>$i</option>";
		}

		echo "</select></th>";
		echo "</tr>";
		echo "</table>";
		echo "</form><br>";

		echo "<table class='main'>";
	    echo "<tr>";

		echo "<th>".t("Laji")."</td>";
		echo "<th>".t("Päivämäärä")."</td>";
		echo "<th>".t("Aika")."</td>";
		echo "<th>".t("Vene")."</td>";
		echo "<th>".t("Kippari")."</td>";
		echo "<th>".t("Nimi")."</td>";
		echo "<th>".t("Kommentit")."</td>";
		echo "</tr>";

		$query = "	SELECT varaukset.paivam, varaukset.vene, varaukset.kalenteri,
					varaukset.pass, varaukset.kommentit,
					ifnull(kuka.pinnamies, varaukset.pinnamies) pin,
					ifnull(kuka.nimi, varaukset.pinnamies) nimi
		          	FROM varaukset
					LEFT JOIN kuka ON (varaukset.pinnamies=kuka.tunnus)
		          	WHERE Year(paivam) = $vuosi
		          	ORDER BY kalenteri, paivam, pass, vene";
		$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

		// csv-tiedostoa varten
		$rivi = "";

		while ($row = mysql_fetch_assoc($result)) {
			echo "<tr>";
			echo "<td>{$KALENTERIT_ARRAY[$row["kalenteri"]]}</td>";
			echo "<td>".tv1dateconv($row["paivam"])."</td>";
			echo "<td>".$AIKA_ARRAY[$row["kalenteri"]][$row["pass"]]."</td>";
			echo "<td>".$VENE_ARRAY[$row["kalenteri"]][$row["vene"]]."</td>";
			echo "<td>".$row["pin"]."</td>";
			echo "<td>".$row["nimi"]."</td>";
			echo "<td>$row[kommentit]</td>";
			echo "</tr>";

			$rivi .= "\"{$KALENTERIT_ARRAY[$row["kalenteri"]]}\",";
			$rivi .= "\"".tv1dateconv($row["paivam"])."\",";
			$rivi .= "\"".$AIKA_ARRAY[$row["kalenteri"]][$row["pass"]]."\",";
			$rivi .= "\"".$VENE_ARRAY[$row["kalenteri"]][$row["vene"]]."\",";
			$rivi .= "\"".$row["pin"]."\",";
			$rivi .= "\"".$row["nimi"]."\",";
			$rivi .= "\"$row[kommentit]\"";
			$rivi .= "\r\n";
		}

		echo "</table><br><br>";

		if ($rivi != "") {

			$otsikot  = "";
			$otsikot .= "\"".t("Laji")."\",";
			$otsikot .= "\"".t("Päivämäärä")."\",";
			$otsikot .= "\"".t("Aika")."\",";
			$otsikot .= "\"".t("Vene")."\",";
			$otsikot .= "\"".t("Kippari")."\",";
			$otsikot .= "\"".t("Nimi")."\",";
			$otsikot .= "\"".t("Kommentit")."\"";
			$otsikot .= "\r\n";

			$filenimi = md5(uniqid(rand(),true)).".csv";

			file_put_contents("/tmp/$filenimi", $otsikot.$rivi);

			echo "<table>";
			echo "<tr><th>".t("Tallenna tulos").":</th>";
			echo "<form method='post'>";
			echo "<input type='hidden' name='tee' value='lataa_tiedosto'>";
			echo "<input type='hidden' name='kaunisnimi' value='Tilastot_$vuosi.csv'>";
			echo "<input type='hidden' name='tmpfilenimi' value='$filenimi'>";
			echo "<td class='back'><input type='submit' value='".t("Tallenna")."'></td></tr></form>";
			echo "</table><br>";
		}
	}
	else {
		echo "<br><br>",t("Tämä raportti vaatii admin-käyttöoikeudet")."!<br>";
	}

	require('inc/footer.inc');
?>