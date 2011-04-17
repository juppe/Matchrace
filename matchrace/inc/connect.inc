<?php

require("salasanat.php");

$link = mysql_connect($dbhost, $dbuser, $dbpass) or die ("Ongelma tietokantapalvelimessa $dbhost");
mysql_select_db($dbkanta) or die ("Tietokantaa $dbkanta löydy palvelimelta (connect.inc)!");

?>