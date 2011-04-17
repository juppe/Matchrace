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
        echo "<b>Luo uusi käyttäjä:</b></font>";
        echo "<form action='$PHP_SELF?session=$session' method='post'>";
		echo "<input type='hidden' name='tee' value='UU'>";
        echo "<table style='border:solid 1px #757B81;' cellspacing='1' cellpadding='4' width='500'>";

        echo "<tr><th align='left'>Käyttäjätunnus:</th><td><input type='text' size='35' name='usercode' maxlength='10'>$tdfont (required)</font></td></tr>";
        echo "<tr><th align='left'>Salasana:</th><td><input type='password' size='35' maxlength='10' name='passwd1'>$tdfont (required)</font></td></tr>";
        echo "<tr><th align='left'>Salasana (uudestaan):</th><td><input type='password' size='35' maxlengt='10' name='passwd2'>$tdfont (required)</font></td></tr>";
        echo "<tr><th align='left'>Nimi:</th><td><input type='text' size='35' maxlength='35' name='firname'>$tdfont (required)</font></td></tr>";
        echo "<tr><th align='left'>Alias:</th><td><input type='text' size='35' maxlength='3' name='pinnamies'>$tdfont (required)</font></td></tr>";
		echo "<tr><th align='left'>Puhelin:</td><td><input type='text' size='35' maxlength='35' name='phonenum'></td></tr>";
        echo "<tr><th align='left'>E-Mail:</th><td><input type='text' size='35' maxlength='35' name='email'></td></tr>";
        echo "<tr><th></th><th><input type='submit' value='Submit'></th></tr>";
        echo "</table></form>";
	}

	if($tee == 'UU'){
		$passwd1 = trim($passwd1);
		$passwd2 = trim($passwd2);
		$usercode = trim($usercode);

		if(strlen($passwd1) >= 5) {
        	$query = "SELECT kuka
        	          FROM kuka
        	          WHERE kuka='$usercode'";
        	$result = mysql_query ($query)
        		or die ("Query failed");
			if (mysql_num_rows($result) == 0) {
				if($passwd1 == $passwd2) {
	    			if($usercode != '' && $passwd1 != '' && $pinnamies != '' && $pinnamies != ''){
	        	    	$query = "INSERT into kuka SET
								kuka = '$usercode',
		                        salasana = '$passwd1',
		                        pinnamies = '$pinnamies',
								nimi = '$firname',
		                       	puhno = '$phonenum',
								access = '10',
		                        eposti = '$email'";

	                	$result = mysql_query ($query)
	                        or die ("Insert failed");
	             		echo "<br><br><br>$thfont Your account has successfully been created!";
						echo "<META HTTP-EQUIV='Refresh'CONTENT='1;URL=main.php?session=$session'>";
	          		}
	            	else {
	               		echo "<br><br>You did not enter all the required information!
	               		<br>Please click the Back button and fill in all the required information.";
	           		}
	       		}
				else {
	       			echo "<br><br>Your passwords does not match!
	        	   	<br>Please click the Back button and retype your passwords.";
	       		}
			}
			else {
	    	  	echo "<br><br>Your usercode is reserved!
	    	    <br>Please click the Back button and fill in a new usercode.";
	    	}
		}
		else {
    	    echo "<br><br>Your password is too short!
    	    <br><br>Please click the Back button and fill in a password that is at least 5 characters long.";
		}
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<form><input type='button' value='Back' onClick='history.go(-1)'></form>";
	}
	
	echo "</td></tr>";
	echo "<tr><td class='back'><br></td></tr>";
	echo "<tr><td class='back'><a href='main.php?day=1&month=$xpvm[1]&year=$xpvm[0]'>Takaisin kalenteriin</a></td></tr>";
	echo "</table>";
	echo "</body></html>";
?>