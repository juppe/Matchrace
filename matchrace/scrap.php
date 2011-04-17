<?php
	
	// Parametrit
	require('inc/parametrit.inc');

	$xpvm = explode("-",$paivam);                	
	
	echo "<table class='main' align='center'>";
	echo "<tr><td class='back' valign='bottom' nowrap><img src='logo2.gif'></td><td class='back' nowrap><h1>MRC - Varauskalenteri: ". $MONTH_ARRAY[$month] ." $year</h1></td></tr>";
	echo "<tr><td class='back'><a href='main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>Takaisin kalenteriin</a></td></tr>";
	echo "<tr><td class='back'><br></td></tr>";
	echo "<tr><td class='back' colspan='2'>";
	            
	if ($tee == "INSERT") {
		if($subject != '' && $message != '') {
			$subject = strip_tags($subject);
			$message = strip_tags($message, '<a><b><i><u><br>');
			$ipos = getenv ("REMOTE_ADDR");
			
			$query = "INSERT into scrapbookmatch set subject='$subject', message='$message', user='$nimi', time=now(), class='top', address='$ipos'";
			$result = mysql_query($query);
			$tee = '';
		}
		else {
			$tee = 'LISAA';
			$virhe = "VIRHE! T‰yt‰ kaikki kent‰t!<br>";
		}
	}
	if ($tee == "INSERTREPLY") {
		if($id != '' && $message != '') {
			$subject = strip_tags($subject);
			$message = strip_tags($message, '<a><b><i><u><br>');
			$ipos = getenv ("REMOTE_ADDR");
			
			$query = "INSERT into scrapbookmatch set message='$message', user='$nimi', time=now(), class='$id', address='$ipos'";
			$result = mysql_query($query);
			$tee = '';
		}
		else {
			$tee = 'REPLY';
			$virhe = "VIRHE! T‰yt‰ kaikki kent‰t!<br>";
		}
	}
	
	if ($tee == '') {
		echo "<a href='$PHP_SELF?tee=LISAA&session=$session'>Lis‰‰ uusi viesti</a><br><br>";
	}
	
	if ($tee == '') {
		$query = "	SELECT * from scrapbookmatch 
					WHERE class='top' 
					ORDER BY time desc";
		$result = mysql_query($query);
		
		echo "<table style='border:solid 1px #757B81;' cellspacing='1' cellpadding='4' width='500'>";
		
		while($row = mysql_fetch_array($result)) {

			echo "<tr><td class='scrap' align='right'>Aihe:</td><td class='scrap'><b>$row[subject]</b></td><td class='scrap'>$row[time]</td><td class='scrap'><a href='$PHP_SELF?tee=REPLY&id=$row[id]&session=$session'>Vastaa t‰h‰n viestiin</a></td></tr>";
			echo "<tr><th></th><td class='normal' colspan='3'><pre>$row[message]</pre><p><b>$row[user]</b></td></tr>";	
			
			$query = "	SELECT * from scrapbookmatch 
					WHERE class='$row[id]' 
					ORDER BY id";
			$rresult = mysql_query($query);
			while($rrow = mysql_fetch_array($rresult)) {
				echo "<tr><th></th><td class='scrap'>Re: <b>$row[subject]</b></td><td class='scrap'>$rrow[time]</td><td class='scrap'></td></tr>";
				echo "<tr><th></th><td class='normal' colspan='3'><pre>$rrow[message]</pre><p><b>$rrow[user]</b></td></tr>";
			}
			echo "<tr><td class='back'>&nbsp;</td></tr>";					
		}        
		
		echo "</table>";
	}
	if ($tee == 'LISAA') {
		echo "<p><p>$virhe<p>
		<table>
		<form method='post' action='$PHP_SELF'>
		<input type='hidden' name='tee' value='INSERT'>
		<tr><td>SUBJECT:</td><td><input type='text' name='subject' value='$subject'></td></tr>
		<tr><td>MESSAGE:</td><td><textarea name='message' wrap='hard' cols='45' rows='10'>$message</textarea></td></tr>
		<tr><td>NAME:</td><td>$nimi</td></tr>
		<tr><td></td><td><input type='submit' value='Lis‰‰'></td></tr>
		</form></table><p><p>";
   }
   if ($tee == 'REPLY') {
   	    echo "<p><p>$virhe<p>
   	    <table>
		<form method='post' action='$PHP_SELF'>
		<input type='hidden' name='tee' value='INSERTREPLY'>
		<input type='hidden' name='id' value='$id'>
		<tr><td>MESSAGE:</td><td><textarea name='message' wrap='hard' cols='45' rows='10'>$message</textarea></td></tr>
		<tr><td>NAME:</td><td>$nimi</td></tr>
		<tr><td></td><td><input type='submit' value='Lis‰‰'></td></tr>
		</form></table><p><p>";
	}
	
	echo "</td></tr>";
	echo "<tr><td class='back'><br></td></tr>";
	echo "<tr><td class='back'><a href='main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>Takaisin kalenteriin</a></td></tr>";
	echo "</table>";
	echo "</body></html>";
	
?>        