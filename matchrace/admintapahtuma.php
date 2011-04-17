<?php

	include('inc/parametrit.inc');
	echo "$headerfont<b>Match Race Booking Calendar<hr>";

	if ($tee == 'UV') {
		echo "<br><br><table bgcolor='$tablecolor' width='500' border='1' cellpadding='10' cellspacing='0'>";
        echo "<form action='$PHP_SELF' method='post'>";
		echo "<input type='hidden' name='pinnamies' value='$pinnamies'>";
		echo "<input type='hidden' name='tee' value='UV2'>";
		echo "<input type='hidden' name='session' value='$session'>";
       	echo "<input type='hidden' name='paivam' value='$paivam'>";
		echo "<input type='hidden' name='tark' value='$tark'>";
      	echo "<input type='hidden' name='varausid' value='$varausid'>";
       	echo "<input type='hidden' name='pass' value='$pass'>";

		$query = "SELECT kuka.pinnamies, kuka.nimi
                          FROM varaukset, kuka
                          WHERE varaukset.pinnamies='$pinnamies' and varaukset.pinnamies=kuka.tunnus";
                $result = mysql_query($query);
		$row = mysql_fetch_row($result);

		echo "	<tr><td>$thfont Varataan k‰ytt‰j‰lle:</td><td>$thfont $row[1]</td></tr>
				<tr><td>$thfont P‰iv‰m‰‰r‰: </td><td>$thfont $paivam</td></tr>
				<tr><td>$thfont Alias: </td><td>$thfont $row[0]</td></tr>
				<tr valign='top'><td>$thfont Varatut veneet:</td><td>";

		$query = " SELECT vene, varaukset.pinnamies, kuka.pinnamies, kuka.nimi
                   FROM varaukset, kuka
                   WHERE paivam='$paivam' and pass='$pass' and varaukset.pinnamies=kuka.tunnus
                   ORDER BY vene";
                $result = mysql_query($query);
		if (mysql_num_rows($result) > 0){
			while ($row = mysql_fetch_row($result)){
				echo "$thfont Vene #$row[0] $row[3] ($row[2])<br>";
			}
		}
		else {
			echo "$thfont Kaikki veneet ovat vapaita";
		}


		echo "</td></tr><tr><td>$thfont Valitse vapaa vene: </td>";

		$query = "SELECT vene, pinnamies
	                  FROM varaukset
	                  WHERE paivam='$paivam' and pass='$pass'
	                  ORDER BY vene";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);

		if (mysql_num_rows($result) < 6) {
			echo "<td><select name='vene'>";
			for($a = 1; $a <= 6; $a++){
				$query = "SELECT vene
                         		  FROM varaukset
                         		  WHERE paivam='$paivam' and pass='$pass' and vene='$a'
                        		  ORDER BY vene";
                		$sresult = mysql_query($query);
				if(mysql_num_rows($sresult) == 0){
					echo "<option value='$a'>Vene $a</option>";
				}
			}
			echo "</select></td></tr><tr><td></td><td><input type='submit' value='Varaa'></td></tr></form>";

		}
		else {
			echo "<td><b>$thfont Kaikki veneet ovat varattuja!</b></td></tr>";
			echo "<tr><td>$thfont Mene takaisin ja vaitse toinen aika: </td><td><form><input type='button' value='Takaisin' onClick='history.go(-1)'></td></tr></form>";
		}
		echo "</table>";
	}
	if ($tee == 'UV2'){

		$query = "	INSERT into varaukset
			  		SET vene='$vene',
			      	pass='$pass',
			      	paivam='$paivam',
			      	pinnamies='$pinnamies'";
        $result = mysql_query($query);
		$idv = mysql_insert_id();

		if($tark == 'editoi' && $varausid != ''){
			$query = "DELETE from varaukset
				  where id='$varausid'";
			$result = mysql_query($query);
			echo "<META HTTP-EQUIV='Refresh'CONTENT='0;URL=edit.php?session=$session&varausid=$idv'>";
		}
		else{
			echo "<META HTTP-EQUIV='Refresh'CONTENT='0;URL=main.php?session=$session'>";
		}
	}
	
	if($tee == ''){
		$query = "	SELECT pinnamies, tunnus, nimi
			  		FROM kuka
			  		WHERE session='$session'";
      	$result = mysql_query($query);
		$row = mysql_fetch_row($result);

		echo "<br><br>";
		echo "<table bgcolor='$tablecolor' width='500' border='1' cellpadding='10' cellspacing='0'>";
		echo "<form action='$PHP_SELF' method='get'>";
		echo "<input type='hidden' name='session' value='$session'>";
		echo "<input type='hidden' name='pinnamies' value='$row[1]'>";
		echo "<input type='hidden' name='tee' value='UV'>";

		echo "<input type='hidden' name='paivam' value='$paivam'>";

		echo "<input type='hidden' name='tark' value='$tark'>";
		echo "<input type='hidden' name='varausid' value='$varausid'>";
		echo "<tr><td>$thfont Varataan k‰ytt‰j‰lle:</td><td>$thfont $row[2]</td></tr>";
		if($tark == 'editoi'){
			echo "<td>$thfont P‰iv‰m‰‰r‰: <select name='paivam'>";
			for($i = -15; $i <= 15; $i++){
				$today = mktime (0,0,0,date("m"), date("d"), date("Y"));
				$stamp  = mktime (0,0,0,date("m"), date("d")+$i, date("Y"));
				$d1 = date(d, $stamp);
                        	$m1 = date(m, $stamp);
                        	$y1 = date(Y, $stamp);
				$dd1 = date(l, $stamp);
				$paivam = $y1."-".$m1."-".$d1;
				$paivam2 = $y1."-".$m1."-".$d1. " ".$dd1;
				if($stamp == $today){
                        		echo "<option value='$paivam' SELECTED>$paivam2</font>";
				}
				else{
					echo "<option value='$paivam'>$paivam2";
				}
			}
			echo "</select></td>";
		}
		else{
			echo "<tr><td>$thfont P‰iv‰m‰‰r‰: </td><td>$thfont $paivam</td></tr>";
		}

		echo "<tr><td>$thfont Alias: </td><td>$thfont $row[0]</td></tr><tr>
			<td>$thfont Valitse aika:</td><td><select name='pass'>
			<option value='1'>10-16
			<option value='2'>16-19
			<option value='3'>19-21
			</select>";
		echo "</td></tr><tr><td></td><td><input type='submit' value='L‰het‰'></tr></table>";
	}


echo "</body></html>";
?>
