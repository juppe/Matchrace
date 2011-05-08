<?php
	ob_start();
	require ("inc/parametrit.inc");

	$query = "UPDATE kuka set session='', kesken='' where session='$session'";
	$result = mysql_query($query) or pupe_error($query);

	$bool = setcookie("matchrace_session", "", time()-432000);

	ob_end_flush();

	if ($bool === TRUE) {
		echo "<script>setTimeout(\"parent.location.href='$palvelin2'\",0);</script>";
	}
	else {
		echo t("Selaimesi ei ilmeisesti tue cookieta").".";
	}

?>