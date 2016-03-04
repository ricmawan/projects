<?php
	if(isset($_POST['hdnId'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Id = $_POST['hdnId'];
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$IsEdit = $_POST['hdnIsEdit'];
		$txtTanggal = explode('-', $_POST['hdnTanggal']);
		$_POST['hdnTanggal'] = "$txtTanggal[2]-$txtTanggal[1]-$txtTanggal[0]"; 
		$txtTanggal = $_POST['hdnTanggal'];
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {
			mysql_query("START TRANSACTION", $dbh);
			mysql_query("SET autocommit=0", $dbh);
			$DetailID = "";
			$Message = "Data Berhasil Disimpan";
			$MessageDetail = "";
			$FailedFlag = 0;

			if($IsEdit == "0") {
				$State = 1;
				$sql = "INSERT INTO transaksi_kas
					(
						TransaksiID,
						Tanggal,
						CreatedDate,
						CreatedBy
					)
					VALUES
					(
						0,
						'".$txtTanggal."',
						NOW(),
						'".$_SESSION['Username']."'
					)";

				if (! $result = mysql_query($sql, $dbh)) {
					$Message = "Terjadi Kesalahan Sistem";
					$MessageDetail = mysql_error();
					$FailedFlag = 1;
					echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
					mysql_query("ROLLBACK", $dbh);
					return 0;
				}
				else $Id = mysql_insert_id($dbh);		
			}

			else {
				$State = 2;
				$sql = "UPDATE 
						transaksi_kas
					SET
						Tanggal = '".$txtTanggal."',
						ModifiedBy = '".$_SESSION['Username']."'
					WHERE
						TransaksiID = ".$Id;

				if (! $result = mysql_query($sql, $dbh)) {
					$Message = "Terjadi Kesalahan Sistem";
					$MessageDetail = mysql_error();
					$FailedFlag = 1;
					echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
					mysql_query("ROLLBACK", $dbh);
					return 0;
				}
			}

			for($i=1;$i<=$RecordNew;$i++) {
				$DetailID .= $_POST['hdnDetailID'.$i].",";
			}
			$DetailID = substr($DetailID, 0, -1);
			//echo $DetailID;
			$State = 3;
			$sql = "DELETE 
				FROM 
					transaksi_rincikas
				WHERE
					DetailID NOT IN($DetailID)			 
					AND TransaksiID = $Id";

			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			for($j=1;$j<=$RecordNew;$j++) {
				if($_POST['hdnDetailID'.$j] == "0") {
					$State = 4;
					$sql = "INSERT INTO transaksi_rincikas
						(
							DetailID,
							TransaksiID,
							Keterangan,
							Jumlah,
							CreatedDate,
							CreatedBy
						)
						VALUES
						(

							0,
							".$Id.",
							'".$_POST['txtKeterangan'.$j]."',
							".str_replace(",", "", $_POST['txtJumlah'.$j]).",
							NOW(),
							'".$_SESSION['Username']."'
						)";
				}
				else {
					$State = 5;
					$sql = "UPDATE 
							transaksi_rincikas
						SET
							Keterangan = '".$_POST['txtKeterangan'.$j]."',
							Jumlah = ".str_replace(",", "", $_POST['txtJumlah'.$j]).",
							ModifiedBy = '".$_SESSION['Username']."'
						WHERE
							DetailID = ".$_POST['hdnDetailID'.$j];
				}

				if (! $result = mysql_query($sql, $dbh)) {
					$Message = "Terjadi Kesalahan Sistem";
					$MessageDetail = mysql_error();
					$FailedFlag = 1;
					echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
					mysql_query("ROLLBACK", $dbh);
					return 0;					
				}							
			}
			echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
			mysql_query("COMMIT", $dbh);
			return 0;
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
