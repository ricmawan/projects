<?php
	$dbuser="root";
	$dbpass="";
	$dbname="lptunika";
	$host="localhost";
	if (! $dbh = mysql_connect($host, $dbuser, $dbpass)) {
		echo mysql_error();
		return 0;
	}

	if (! mysql_select_db($dbname)) {
		echo mysql_error();
		return 0;
	}
?>