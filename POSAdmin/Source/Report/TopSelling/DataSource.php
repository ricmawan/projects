<?php
	header('Content-Type: application/json');
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";
	if(ISSET($_POST['CategoryID']))
	{
		$FailedFlag = 0;
		$CategoryID = $_POST['CategoryID'];
		if($_POST['txtFromDate'] == "") {
			$txtFromDate = "2000-01-01";
		}
		else {
			$txtFromDate = explode('-', mysql_real_escape_string($_POST['txtFromDate']));
			$_POST['txtFromDate'] = "$txtFromDate[2]-$txtFromDate[1]-$txtFromDate[0]"; 
			$txtFromDate = $_POST['txtFromDate'];
		}
		if($_POST['txtToDate'] == "") {
			$txtToDate = date("Y-m-d");
		}
		else {
			$txtToDate = explode('-', mysql_real_escape_string($_POST['txtToDate']));
			$_POST['txtToDate'] = "$txtToDate[2]-$txtToDate[1]-$txtToDate[0]"; 
			$txtToDate = $_POST['txtToDate'];
		}
		//kolom di table
		$sql = "CALL spSelTopSellingReport(".$CategoryID.", '".$txtFromDate."', '".$txtToDate."', '".$_SESSION['UserLogin']."')";

		if (! $result = mysqli_query($dbh, $sql)) {
			logEvent(mysqli_error($dbh), '/Report/TopSelling/DataSource.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
			$FailedFlag = 1;
			$json_data = array(
						"FailedFlag"		=> $FailedFlag
					);
			echo json_encode($json_data);
			return 0;
		}
		
		$xArray = array();
		$yArray = array();
		while ($row = mysqli_fetch_array($result)) {
			array_push($xArray, $row['ItemName']);
			array_push($yArray, $row['SellingCount']);
		}
		
		mysqli_free_result($result);
		mysqli_next_result($dbh);

		$json_data = array(
						"FailedFlag"		=> $FailedFlag,
						"xData"				=> $xArray,
						"yData"				=> $yArray
					);
	}
	
	echo json_encode($json_data);
?>
