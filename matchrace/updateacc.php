<?php
	
	// Parametrit
	require('inc/parametrit.inc');

	$xpvm = explode("-",$paivam);                	
	
	echo "<table class='main' align='center'>";
	echo "<tr><td class='back' valign='bottom' nowrap><img src='logo2.gif'></td><td class='back' nowrap><h1>MRC - Varauskalenteri: ". $MONTH_ARRAY[$month] ." $year</h1></td></tr>";
	echo "<tr><td class='back'><a href='main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>Takaisin kalenteriin</a></td></tr>";
	echo "<tr><td class='back'><br></td></tr>";
	echo "<tr><td class='back' colspan='2'>";

	if($tee == ''){
		$query = "SELECT kuka, superuser
			 FROM kuka where session='$session'";
	        $result = mysql_query ($query)
	        	 or die ("Query25 failed");

		if (mysql_num_rows($result) == 1) {
			$srow = mysql_fetch_row ($result);

			if($srow[1] == 'SUPER' && $edi != ''){
				$srow[0] = $edi;
			}

			$query = "SELECT kuka, nimi, pinnamies, puhno, eposti
				  FROM kuka
			  	  WHERE kuka='$srow[0]'";
	        	$result = mysql_query ($query)
		       		 or die ("Query3 failed");
			$row = mysql_fetch_row ($result);

			echo "<form action='$PHP_SELF?session=$session' method='post'>";
			echo "<table style='border:solid 1px #757B81;' cellspacing='1' cellpadding='4' width='500'>";
		
			echo "<tr><th align='left'>Käyttäjätunnus:</th><td>$thfont $row[0]</td></tr>";
				
			echo "<input type='hidden' name='tee' value='UU'>";
			echo "<input type='hidden' name='usercode' value='$row[0]'>";
		
			echo "<tr><th align='left'>Nimi:</th><td><input type='text' size='35' value='$row[1]' maxlenght='25' name='firname'></td></tr>";
		
			if ($srow[1] == "SUPER") {
				echo "<tr><th align='left'>Alias:</th><td height='25'><input type='text' size='35' value='$row[2]' maxlenght='3' name='alias'></td></tr>";
			}
			else {
				echo "<tr><th align='left'>Alias:</th><td height='25'>$row[2]</td></tr>";
			}
			echo "<tr><th align='left'>Puhelin:</th><td><input type='text' size='35' value='$row[3]' maxlenght='25' name='phonenum'></td></tr>";
			echo "<tr><th align='left'>E-Mail osoite:</th><td><input type='text' size='35' value='$row[4]' maxlenght='25' name='email'></td></tr>";
		
			echo "<tr><th colspan='2' align='left'><br>Jos haluat vaihtaa salasanasi, syötä uusi salasana kahteen kertaan:</th></tr>
				<tr><th align='left'>Uusi salasana:</th><td><input type='password' name='uusi1' size='15' maxlength='30'></td></tr>
				<tr><th align='left'>Uusi salasana:</th><td><input type='password' name='uusi2' size='15' maxlength='30'></td></tr>";
			echo "<tr><th></th><th align='left'><input type='submit' value='Päivitä'></th></tr>";
			echo "</table></form>";
		}
	}
	if($tee == 'UU'){
		$query = "UPDATE kuka
				  SET   nimi='$firname',
				  puhno='$phonenum',
				  eposti='$email'";
	
		if($alias != '') {		  
			$query .=" , pinnamies='$alias' ";
		}
	
		$query .= "WHERE kuka='$usercode'";
		$result = mysql_query ($query)
			or die ("Update failed");

		if (strlen(trim($uusi1)) > 0) {
			if (trim($uusi1) != trim($uusi2)) {
				echo "Uudet salasanasi ovat erilaiset!";
				$err = 1;
			}
			else {
				$query = "UPDATE kuka
						  SET salasana = '$uusi1'
						  WHERE kuka = '$usercode'";
				$result = mysql_query ($query)
					or die ("Salasanapäivitys epäonnistui kuka");
			}
		}

		echo "<br><br><br>Käyttäjätietosi päivitettiin onnistuneesti!";
		echo "<META HTTP-EQUIV='Refresh'CONTENT='1;URL=main.php'>";
	}
	
	echo "</td></tr>";
	echo "<tr><td class='back'><br></td></tr>";
	echo "<tr><td class='back'><a href='main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>Takaisin kalenteriin</a></td></tr>";
	echo "</table>";
	echo "</body></html>";
?>