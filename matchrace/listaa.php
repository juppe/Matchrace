<?php
	
	// Parametrit
	require('inc/parametrit.inc');

	$xpvm = explode("-",$paivam);                	
	
	echo "<table class='main' align='center'>";
	echo "<tr><td class='back' valign='bottom' nowrap><img src='logo2.gif'></td><td class='back' nowrap><h1>MRC - Varauskalenteri: ". $MONTH_ARRAY[$month] ." $year</h1></td></tr>";
	echo "<tr><td class='back'><a href='main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>Takaisin kalenteriin</a></td></tr>";
	echo "<tr><td class='back'><br></td></tr>";
	echo "<tr><td class='back' colspan='2'>";

	$query = "	SELECT upper(pinnamies) pinnamies, nimi, kuka, puhno, eposti
               	FROM kuka
               	WHERE kuka!='' 
				order by pinnamies";
    $result = mysql_query($query);

    echo "<table class='main'>";
	
	if($kukarow["superuser"] == 'SUPER' and $kukarow["access"] < 20){
		echo "<th colspan='4'></th><th>Admin</th><th>Admin</th>";
    }
	echo "</tr>";
	
    while($row = mysql_fetch_row($result)){
		echo "<tr><td>$row[1]</td><th>$row[0]</th><td>$row[3] &nbsp;</td><td>$row[4] &nbsp;</td>";
		
		if($kukarow["superuser"] == 'SUPER' and $kukarow["access"] < 20){
			echo "<td><a href='poista.php?&edi=$row[2]'>Poista käyttäjä</a></td><td><a href='updateacc.php?edi=$row[2]'>Muuta käyttäjän tietoja</a></td>";
		}
		
		echo "</tr>";
	}
	echo "</table>";
	
	echo "</td></tr>";
	echo "<tr><td class='back'><br></td></tr>";
	echo "<tr><td class='back'><a href='main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>Takaisin kalenteriin</a></td></tr>";
	echo "</table>";
	echo "</body></html>";
?>