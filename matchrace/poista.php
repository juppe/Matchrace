<?php

// Parametrit
require('inc/parametrit.inc');

if ($kukarow["superuser"] == 'SUPER' and $kukarow["access"] < 20) {

	if ($tee == '') {
		echo "<br><br>Oletko varma ett� haluat poistaa k�ytt�j�n, '$edi'<br><br>";
		echo "<br><a href='poista.php?edi=$edi&tee=POIS'>Kyll�!</a>&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='listaa.php'>En...</a>";
	}

	if ($tee == 'POIS') {
		$query = "	DELETE
					FROM kuka
					WHERE kuka = '$edi'";
		$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

		echo "<br><br>K�ytt�j� poistettu!";
		echo "<META HTTP-EQUIV='Refresh'CONTENT='1;URL=listaa.php'>";
	}
}

require('inc/footer.inc');

?>