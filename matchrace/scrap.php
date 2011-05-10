<?php

	// Parametrit
	require('inc/parametrit.inc');

	if ($tee == "INSERT") {
		if ($subject != '' and $message != '') {
			$subject = strip_tags($subject);
			$message = strip_tags($message, '<a><b><i><u><br>');
			$ipos = getenv("REMOTE_ADDR");

			$query = "	INSERT into scrapbookmatch
						set subject	= '$subject',
						message		= '$message',
						user		= '$kukarow[kuka]',
						time		= now(),
						class 		= 'top',
						address		= '$ipos'";
			$result = mysql_query($query);
			$tee = '';
		}
		else {
			$tee = 'LISAA';
			$virhe = t("VIRHE: T‰yt‰ kaikki kent‰t")."!<br>";
		}
	}

	if ($tee == "INSERTREPLY") {
		if ($id != '' and $message != '') {
			$subject = strip_tags($subject);
			$message = strip_tags($message, '<a><b><i><u><br>');
			$ipos = getenv("REMOTE_ADDR");

			$query = "	INSERT into scrapbookmatch
						set message	= '$message',
						user		= '$kukarow[kuka]',
						time		= now(),
						class 		= '$id',
						address		= '$ipos'";
			$result = mysql_query($query);
			$tee = '';
		}
		else {
			$tee = 'REPLY';
			$virhe = t("VIRHE: T‰yt‰ kaikki kent‰t")."!<br>";
		}
	}

	if ($tee == '') {
		$query = "	SELECT scrapbookmatch.*, ifnull(kuka.nimi, scrapbookmatch.user) user
					FROM scrapbookmatch
					LEFT JOIN kuka on scrapbookmatch.user=kuka.kuka
					WHERE scrapbookmatch.class = 'top'
					ORDER BY scrapbookmatch.time desc";
		$result = mysql_query($query);

		echo "<h2>".t("Keskustelu").":</h2>";
		echo "<a href='scrap.php?tee=LISAA'>".t("Lis‰‰ uusi viesti")."</a><br><br>";
		echo "<table class='main'>";

		while ($row = mysql_fetch_array($result)) {
			$query = "	SELECT scrapbookmatch.*, ifnull(kuka.nimi, scrapbookmatch.user) user
						FROM scrapbookmatch
						LEFT JOIN kuka on scrapbookmatch.user=kuka.kuka
						WHERE class = '$row[id]'
						ORDER BY id";
			$rresult = mysql_query($query);

			echo "<tr>
					<th rowspan='".(2+mysql_num_rows($rresult)*2)."'></th>
					<td class='scrap'><b>$row[subject]</b></td><td class='scrap'>$row[time]</td>
					<td class='scrap'><a href='scrap.php?tee=REPLY&id=$row[id]'>".t("Vastaa t‰h‰n viestiin")."</a></td></tr>";
			echo "<tr><td class='normal' colspan='3'><pre>$row[message]</pre>$row[user]</td></tr>";

			while ($rrow = mysql_fetch_array($rresult)) {
				echo "<tr><td class='scrap'>Re: <b>$row[subject]</b></td><td class='scrap'>$rrow[time]</td><td class='scrap'></td></tr>";
				echo "<tr><td class='normal' colspan='3'><pre>$rrow[message]</pre>$rrow[user]</td></tr>";
			}
			echo "<tr><td class='back'>&nbsp;</td></tr>";
		}

		echo "</table>";
	}

	if ($tee == 'LISAA') {
		echo "<p><p>$virhe<p>
		<table>
		<form method='post' >
		<input type='hidden' name='tee' value='INSERT'>
		<tr><td>".t("Aihe").":</td><td><input type='text' name='subject' value='$subject'></td></tr>
		<tr><td>".t("Viesti").":</td><td><textarea name='message' wrap='hard' cols='45' rows='10'>$message</textarea></td></tr>
		<tr><td>".t("Nimi").":</td><td>$kukarow[nimi]</td></tr>
		<tr><td></td><td><input type='submit' value='".t("Lis‰‰")."'></td></tr>
		</form></table><p><p>";
   }

	if ($tee == 'REPLY') {
   	    echo "<p><p>$virhe<p>
   	    <table>
		<form method='post' >
		<input type='hidden' name='tee' value='INSERTREPLY'>
		<input type='hidden' name='id' value='$id'>
		<tr><td>".t("Viesti").":</td><td><textarea name='message' wrap='hard' cols='45' rows='10'>$message</textarea></td></tr>
		<tr><td>".t("Nimi").":</td><td>$kukarow[nimi]</td></tr>
		<tr><td></td><td><input type='submit' value='".t("Lis‰‰")."'></td></tr>
		</form></table><p><p>";
	}

	require('inc/footer.inc');
?>