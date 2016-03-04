<?php
	if(isset($_POST['hdnId'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Id = $_POST['hdnId'];
		$Nama = $_POST['txtNama'];
		$Username = $_POST['txtUsername'];
		$Jabatan = $_POST['ddlJabatan'];
		$Password = $_POST['txtPassword'];
		$hdnMenuID = $_POST['hdnMenuID'];
		$hdnEditMenuID = $_POST['hdnEditMenuID'];
		$hdnDeleteMenuID = $_POST['hdnDeleteMenuID'];
		$hdnIsEdit = $_POST['hdnIsEdit'];
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {
			$MenuID = "";
			$EditMenuID = "";
			$DeleteMenuID = "";
			/*for($i=0; $i<count($hdnMenuID); $i++) {
				$MenuID .= $hdnMenuID[$i].",";
				$EditMenuID .= $hdnEditMenuID[$i].",";
				$DeleteMenuID .= $hdnDeleteMenuID[$i].",";
			}
			$MenuID = substr($MenuID, 0, -1);
			$EditMenuID = substr($EditMenuID, 0, -1);
			$DeleteMenuID = substr($DeleteMenuID, 0, -1);
			
			echo $DeleteMenuID;*/
	
			if($Password == "") $Password = $_SESSION['Password'];
			else $Password = MD5($Password);
	
			$sql = "CALL spInsUser(".$Id.", '".$Nama."', '".$Username."', '".$Jabatan."', '".$Password."', '".$hdnMenuID."', '".$hdnEditMenuID."', '".$hdnDeleteMenuID."', ".$hdnIsEdit.", '".$_SESSION['Username']."')";
			
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_row($result);
			
			if($row[3] == 0 && $_SESSION['UserID'] == $Id) {
				$_SESSION['Password'] = $Password;
				$_SESSION['Username'] = $Username;
			}
			
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