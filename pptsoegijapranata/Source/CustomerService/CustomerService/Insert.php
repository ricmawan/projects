<?php
	if(isset($_POST['hdnId'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Id = $_POST['hdnId'];
		$hdnIsEdit = $_POST['hdnIsEdit'];
		
		$txtTanggal = explode('-', $_POST['txtTanggal']);
		$_POST['txtTanggal'] = "$txtTanggal[2]-$txtTanggal[1]-$txtTanggal[0]"; 
		$txtTanggal = $_POST['txtTanggal'];
		
		$ddlJenis = $_POST['ddlJenis'];
		$ddlKlien = $_POST['ddlKlien'];
		
		if(isset($_POST['txtMaksKlien']) && $_POST['txtMaksKlien'] != "") $txtMaksKlien = $_POST['txtMaksKlien'];
		else $txtMaksKlien = 0;
		
		if(isset($_POST['txtKeluhan'])) $txtKeluhan = $_POST['txtKeluhan'];
		else $txtKeluhan = "";
		
		if(isset($_POST['txtPrognosis'])) $txtPrognosis = $_POST['txtPrognosis'];
		else $txtPrognosis = "";
		
		if(isset($_POST['txtKeterangan'])) $txtKeterangan = $_POST['txtKeterangan'];
		else $txtKeterangan = "";
		
		if(isset($_POST['cbxReport'])) $cbxReport = $_POST['cbxReport'];
		else $cbxReport = 0;
		
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {
			
			$sql = "CALL spInsCustomerService(
								".$Id.", 
								'".$txtTanggal."', 
								".$ddlJenis.", 
								".$ddlKlien.", 
								".$txtMaksKlien.", 
								'".$txtKeterangan."', 
								".$cbxReport.", 
								'".$txtKeluhan."',
								'".$txtPrognosis."', 
								".$hdnIsEdit.", 
								'".$_SESSION['Username']."'
							)";
			
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_row($result);			
			echo returnstate($row[0], $row[1], $row[2], $row[3], $row[4]);
		}
	}
	
	function returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = array(
			"Id" => $Id, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"FailedFlag" => $FailedFlag,
			"State" => $State
		);
		return json_encode($data);
	
	}
?>
