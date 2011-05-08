<?php

// Parametrit
require('inc/parametrit.inc');

if ($kukarow["superuser"] == 'SUPER' and $kukarow["access"] < 20) {

	if ($tee == '') {
		echo "<br><br>Oletko varma että haluat poistaa käyttäjän, '$edi'<br><br>";
		echo "<br><a href='poista.php?edi=$edi&tee=POIS'>Kyllä!</a>&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='listaa.php'>En...</a>";
	}

	if ($tee == 'POIS') {
		$query = "	DELETE
					FROM kuka
					WHERE kuka = '$edi'";
		$result = mysql_query($query) or die ("$query<br><br>".mysql_error());

		echo "<br><br>Käyttäjä poistettu!";
		echo "<META HTTP-EQUIV='Refresh'CONTENT='1;URL=listaa.php'>";
	}
}

require('inc/footer.inc');

?>