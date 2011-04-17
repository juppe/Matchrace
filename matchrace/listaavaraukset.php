<?php
	
	// Parametrit
	require('inc/parametrit.inc');

	$xpvm = explode("-",$paivam);                	
	
	echo "<table class='main' align='center'>";
	echo "<tr><td class='back' valign='bottom' nowrap><img src='logo2.gif'></td><td class='back' nowrap><h1>MRC - Varauskalenteri: ". $MONTH_ARRAY[$month] ." $year</h1></td></tr>";
	echo "<tr><td class='back'><a href='main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>Takaisin kalenteriin</a></td></tr>";
	echo "<tr><td class='back'><br></td></tr>";
	echo "<tr><td class='back' colspan='2'>";
	
    echo "<table style='border:solid 1px #757B81;' cellspacing='1' cellpadding='4'>";
	echo "<tr><th colspan='5'>Vanhemmat varaukset: </th>"; 
	echo "</tr>";
    echo "<tr><td>P‰iv‰m‰‰r‰: </td>"; 
	echo "<td>Vene: </td>";
	echo "<td>Aika: </td>";
	echo "<td>Kommentit: </td>";
	echo "<td>Treenin aihe:</td>";
	echo "</tr>";
	
	$query = "SELECT *
	          FROM varaukset
	          WHERE paivam < now() and pinnamies='$kukarow[tunnus]' and Year(paivam)=". date("Y") ."
	          ORDER BY paivam";
	$presult = mysql_query($query) or die($query);
	
	while($row = mysql_fetch_array($presult)){
		echo "<tr><td>".tv1dateconv($row["paivam"])."</td>"; 
		echo "<td>".$VENE_ARRAY[$row["vene"]]."</td>";
		echo "<td>".$AIKA_ARRAY[$row["pass"]]."</td>";
		echo "<td>$row[kommentit]</td>";
		echo "<td>$row[kommentit2]</td>";
		echo "</tr>";
	}
	
	echo "</table><br><br>";
	echo "<table style='border:solid 1px #757B81;' cellspacing='1' cellpadding='4'>";
	echo "<tr><th colspan='5'>Tulevat varaukset: </th>"; 
	echo "</tr>";
    echo "<tr><td>P‰iv‰m‰‰r‰: </td>"; 
	echo "<td>Vene: </td>";
	echo "<td>Aika: </td>";
	echo "<td>Kommentit: </td>";
	echo "<td>Treenin aihe:</td>";
	echo "</tr>";
	
	$query = "SELECT *
	          FROM varaukset
	          WHERE paivam >= now() and pinnamies='$kukarow[tunnus]' and Year(paivam)=". date("Y") ."
	          ORDER BY paivam";
	$result = mysql_query($query) or die($query);
	
	while($row = mysql_fetch_array($result)){
		echo "<tr><td>".tv1dateconv($row["paivam"])."</td>"; 
		echo "<td>".$VENE_ARRAY[$row["vene"]]."</td>";
		echo "<td>".$AIKA_ARRAY[$row["pass"]]."</td>";
		echo "<td>$row[kommentit]</td>";
		echo "<td>$row[kommentit2]</td>";
		echo "</tr>";
	}
	echo "</table>";
	
	echo "</td></tr>";
	echo "<tr><td class='back'><br></td></tr>";
	echo "<tr><td class='back'><a href='main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>Takaisin kalenteriin</a></td></tr>";
	echo "</table>";
	echo "</body></html>";	
?>