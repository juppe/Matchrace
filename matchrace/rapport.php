<?php

if($tee == "Y") {
	$filename="testi.csv"; 
	//header("Content-type: application/force-download");
	//header("Content-Disposition: attachment; filename=".$filename);
	//header("Content-Description: File Transfert");
	
	$dbhost='localhost';
	$dbuser='juppe';
	$dbpass='juppe1';
	$dbkanta='matchrace';
	
	$link = mysql_pconnect ($dbhost, $dbuser, $dbpass)
		or die ("Ongelma tietokantapalvelimessa $dbhost");
	mysql_select_db ($dbkanta)
		or die ("Tietokanta $dbkanta ei löydy palvelimelta $dbhost");
	
	$query = "	SELECT  $kentat
				FROM varaukset
				WHERE id >= 0 ";
	
	if(strlen($ehto) > 0) {
		$query .= "and ".str_replace("\\\"", "'", $ehto);
	}
	if(strlen($lisa) > 0) {
		$query .= " ".str_replace("\\\"", "'", $lisa);
	}
	$result = mysql_query($query)
		or die ("FAILED: $query");
	
	echo "\n";
	for($i=0; $i<mysql_num_fields($result);$i++) {			
		echo "\"".mysql_field_name($result,$i)."\"\t";
	}
	echo "\n";
	while ($row = mysql_fetch_array($result)){
		for($i=0; $i<mysql_num_fields($result);$i++) {			
			echo "\"".str_replace("\r\n", "<br>", $row[$i])."\"\t";
		}
		echo "\n";
	}
}

if ($tee == '') {
	include('inc/parametrit.inc');
	echo "<h1>Aja raportti<hr></h1>";
	echo "
		<table><tr>
		<form action = '$PHP_SELF?session=$session&tee=Y' name='kala' method='post'>
		<td>SELECT:&nbsp;</td>
		<td><input type='text' name='kentat' size='80'></td>
		<td></tr>
		<tr><td>WHERE:&nbsp;</td><td><input type='text' name='ehto' size='80'></td></tr>
		<tr><td>LISA:&nbsp;</td><td><input type='text' name='lisa' size='80'></td></tr>
		<tr><td>
		<input type='Submit' value='Valitse'></form>
		</td>
		</tr></table>";
	$formi = "kala";
	$kentta = "kentat";
}
//include('inc/footer.inc');

?>