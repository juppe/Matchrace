<?php

	// Parametrit
	require('inc/parametrit.inc');

	$query = "	SELECT upper(pinnamies) pinnamies, nimi, kuka, puhno, eposti
               	FROM kuka
               	WHERE kuka != ''
				ORDER BY pinnamies";
    $result = mysql_query($query);

	echo "<h2>".t("K�ytt�j�t").":</h2>";

    echo "<table class='main'>";

    while ($row = mysql_fetch_assoc($result)) {
		echo "<tr><td>$row[nimi]</td><th>$row[pinnamies]</th><td>$row[puhno]</td><td>$row[eposti]</td>";

		if ($kukarow["superuser"] == 'SUPER' and $kukarow["access"] < 20) {
			echo "<td><a href='poista.php?&edi=$row[kuka]'>".t("Poista k�ytt�j�")."</a></td>";
			echo "<td><a href='updateacc.php?edi=$row[kuka]'>".t("Muuta k�ytt�j�n tietoja")."</a></td>";
		}

		echo "</tr>";
	}

	echo "</table>";

	require('inc/footer.inc');
?>