<?php

	// Parametrit
	require('inc/parametrit.inc');

	echo "<h2>".t("Omat varaukset").":</h2>";

	echo "<table class='main'>";
	echo "<tr><th colspan='6'>".t("Tulevat varaukset").":</th>";
	echo "</tr>";
    echo "<tr><td>".t("P‰iv‰m‰‰r‰")."</td>";
	echo "<td>".t("Laji")."</td>";
	echo "<td>".t("Vene")."</td>";
	echo "<td>".t("Aika")."</td>";
	echo "<td>".t("Kommentit")."</td>";
	echo "<td>".t("Treenin aihe")."</td>";
	echo "</tr>";

	$query = "	SELECT *
	          	FROM varaukset
	          	WHERE paivam >= now()
				and pinnamies = '$kukarow[tunnus]'
				and Year(paivam) = ".date("Y")."
	          	ORDER BY paivam";
	$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

	while ($row = mysql_fetch_assoc($result)) {
		echo "<tr><td>".tv1dateconv($row["paivam"])."</td>";
		echo "<td>{$KALENTERIT_ARRAY[$row["kalenteri"]]}</td>";
		echo "<td>".$VENE_ARRAY[$row["kalenteri"]][$row["vene"]]."</td>";
		echo "<td>".$AIKA_ARRAY[$row["kalenteri"]][$row["pass"]]."</td>";
		echo "<td>$row[kommentit]</td>";
		echo "<td>$row[kommentit2]</td>";
		echo "</tr>";
	}
	echo "</table><br><br>";

	echo "<table class='main'>";
	echo "<tr><th colspan='6'>".t("Vanhemmat varaukset").":</th>";
	echo "</tr>";
    echo "<tr><td>".t("P‰iv‰m‰‰r‰").":</td>";
	echo "<td>".t("Laji").":</td>";
	echo "<td>".t("Vene").":</td>";
	echo "<td>".t("Aika").":</td>";
	echo "<td>".t("Kommentit").":</td>";
	echo "<td>".t("Treenin aihe").":</td>";
	echo "</tr>";

	$query = "	SELECT *
	          	FROM varaukset
	          	WHERE paivam < now()
				and pinnamies = '$kukarow[tunnus]'
				and Year(paivam) = ".date("Y")."
	          	ORDER BY paivam";
	$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

	while ($row = mysql_fetch_array($result)) {
		echo "<tr><td>".tv1dateconv($row["paivam"])."</td>";
		echo "<td>{$KALENTERIT_ARRAY[$row["kalenteri"]]}</td>";
		echo "<td>".$VENE_ARRAY[$row["kalenteri"]][$row["vene"]]."</td>";
		echo "<td>".$AIKA_ARRAY[$row["kalenteri"]][$row["pass"]]."</td>";
		echo "<td>$row[kommentit]</td>";
		echo "<td>$row[kommentit2]</td>";
		echo "</tr>";
	}

	echo "</table><br><br>";

	require('inc/footer.inc');
?>