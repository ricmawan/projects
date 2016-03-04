<?php
	if(isset($_POST['hdnId'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Id = $_POST['hdnId'];
		$Message = "Data gagal dimasukkan, cek koneksi internet dan coba lagi!";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 0;
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
		
			$Tanggal = $_POST['txtTanggal'];
			if($Tanggal == "") $Tanggal = 0;
			$State = 1;
			$sql = "DELETE 
				FROM 
					piket_asisten
				WHERE
					TanggalPiket NOT IN('$Tanggal')			 
					AND AsistenID = ".$Id."
					AND MONTH(TanggalPiket) = MONTH(NOW())
					AND YEAR(TanggalPiket) = YEAR(NOW())";

			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}
			if($_POST['txtTanggal'] != "") {
				$Tanggal = explode(", ", $Tanggal);
				for($i = 0; $i<sizeof($Tanggal); $i++) {

					//$TanggalTemp = explode('-', $Tanggal[$i]);
					//$Tanggal[$i] = "$TanggalTemp[2]-$TanggalTemp[1]-$TanggalTemp[0]";
					$State = 2;
					$sql = "SELECT
							* 
						FROM
							piket_asisten
						WHERE
							AsistenID = ".$Id."
							AND TanggalPiket = '".$Tanggal[$i]."'";

					if (! $result = mysql_query($sql, $dbh)) {
						$Message = "Terjadi Kesalahan Sistem";
						$MessageDetail = mysql_error();
						$FailedFlag = 1;
						echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
						mysql_query("ROLLBACK", $dbh);
						return 0;
					}

					$rows = mysql_num_rows($result);
					if($rows == 0) {
						$State = 3;
						$sql2 = "INSERT INTO piket_asisten
							(
								PiketID,
								AsistenID,
								TanggalPiket,
								CreatedDate,
								CreatedBy
							)
							VALUES
							(
								0,
								".$Id.",
								'".$Tanggal[$i]."',
								NOW(),
								'".$_SESSION['Username']."'
							)";

						if (! $result2 = mysql_query($sql2, $dbh)) {
							$Message = "Terjadi Kesalahan Sistem";
							$MessageDetail = mysql_error();
							$FailedFlag = 1;
							echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
							mysql_query("ROLLBACK", $dbh);
							return 0;
						}
					}
				}			
				echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("COMMIT", $dbh);
				return 0;
			}
			else echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
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
