<?php
        include('inc/parametrit.inc');


if($tee == ''){
	echo "<br><br>$thfont Oletko varma että haluat poistaa käyttäjän, '$edi'<br><br>";
	echo "<br><a href='poista.php?session=$session&edi=$edi&tee=POIS'>$thfont Kyllä!</a>&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<a href='main.php?session=$session'>$thfont En...</a>";
}
if($tee == 'POIS'){
        $query = "SELECT kuka, superuser
                  FROM kuka
		  WHERE session='$session'";
        $result = mysql_query ($query)
                 or die ("Query25 failed");

        if (mysql_num_rows($result) == 1) {
                $srow = mysql_fetch_row ($result);

                if($srow[1] == 'SUPER' && $edi != ''){
                         $kala = $edi;
                }
		else{
			echo "Surkea yritys";
			exit;
		}

                $query = "DELETE
			  FROM kuka
			  WHERE kuka = '$kala'";
                $result = mysql_query ($query)
                         or die ("Query3 failed");

		echo "<br><br>$thfont Käyttäjä poistettu";
        	echo "<br><br><a href='main.php?session=$session'>$thfont Takaisin kalenteriin</a>";
	}
}

?>