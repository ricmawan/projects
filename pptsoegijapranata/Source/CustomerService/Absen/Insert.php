<?php
	if(isset($_POST['hdnId'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Id = $_POST['hdnId'];
		$Record = $_POST['record'];
		$RecordNew = $_POST['recordnew'];
		$Jenis = $_POST['hdnJenis'];	
		if(isset($_POST['ddlSupervisor'])) $SupervisorID = (string)$_POST['ddlSupervisor'];
		else $SupervisorID = NULL;
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
			for($i=1;$i<=$RecordNew;$i++) {
				$DetailID .= $_POST['hdnDetailID'.$i].",";
			}
			$DetailID = substr($DetailID, 0, -1);
			//echo $DetailID;
			$State = 0;
			$sql = "UPDATE transaksi_customerservice
				SET
					UangMuka = ".str_replace(",", "", $_POST['txtUangMuka']).",
					Pembayaran = ".str_replace(",", "", $_POST['txtPembayaran']).",
					Diskon = ".$_POST['txtDiskon'].",
					Pajak = ".$_POST['txtPajak'].",
					SupervisorID = '".$SupervisorID."',
					Denda = ".str_replace(",", "", $_POST['txtDenda'])."
				WHERE
					TransaksiID = $Id";

			if (! $result = mysql_query($sql, $dbh)) {
				$Message = "Terjadi Kesalahan Sistem";
				$MessageDetail = mysql_error();
				$FailedFlag = 1;
				echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
				mysql_query("ROLLBACK", $dbh);
				return 0;
			}

			$State = 1;
			$sql = "DELETE 
				FROM 
					transaksi_rincicustomerservice 
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
					$State = 2;
					if($Jenis == 2 || $Jenis == 4) {
						$sql = "INSERT INTO transaksi_rincicustomerservice
							(
								DetailID,
								TransaksiID,
								KonsultanID,
								AsistenID,
								TerapisID,
								LayananID,
								Jumlah,
								Harga,
								Harga_Layanan,
								FeeKonsultan,
								Harga_PiketAsisten,
								CreatedDate,
								CreatedBy
							)
							VALUES
							(
								0,
								".$Id.",
								".$_POST['ddlKonsultan'.$j].",
								".$_POST['ddlAsisten'.$j].",
								".$_POST['ddlTerapis'.$j].",
								".$_POST['ddlLayanan'.$j].",
								".$_POST['txtJumlah'.$j].",
								".str_replace(",", "", $_POST['txtHarga'.$j]).",
								".$_POST['hdnHargaLayanan'.$j].",
								".$_POST['hdnFeeKonsultan'.$j].",
								".$ASISTANT_CLIENT_PICKET.",
								NOW(),
								'".$_SESSION['Username']."'
							)";
					}
					else {
						if($_POST['ddlProses'.$j] == 2) $HargaKilat = $_POST['hdnStatusProcess'];
						else $HargaKilat = 0;
						$sql = "INSERT INTO transaksi_rincicustomerservice
							(
								DetailID,
								TransaksiID,
								KonsultanID,
								AsistenID,
								LayananID,
								Nama,
								Pendidikan,
								NoPsikogram,
								Jumlah,
								Harga,
								Harga_Layanan,
								FeeKonsultan,
								StatusProcess,
								Harga_StatusProcess,
								Harga_PiketAsisten,
								CreatedDate,
								CreatedBy
							)
							VALUES
							(
								0,
								".$Id.",
								".$_POST['ddlKonsultan'.$j].",
								".$_POST['ddlAsisten'.$j].",
								".$_POST['ddlLayanan'.$j].",
								'".$_POST['txtNama'.$j]."',
								".$_POST['ddlPendidikan'.$j].",
								'".$_POST['txtPsikogram'.$j]."',
								".$_POST['txtJumlah'.$j].",
								".str_replace(",", "", $_POST['txtHarga'.$j]).",
								".$_POST['hdnHargaLayanan'.$j].",
								".$_POST['hdnFeeKonsultan'.$j].",
								".$_POST['ddlProses'.$j].",
								".$HargaKilat.",
								".$ASISTANT_CLIENT_PICKET.",
								NOW(),
								'".$_SESSION['Username']."'
							)";
					}
				}
				else {
					$State = 3;
					
					if($Jenis == 2 || $Jenis == 4) {
						$sql = "UPDATE 
								transaksi_rincicustomerservice
							SET
								KonsultanID = ".$_POST['ddlKonsultan'.$j].",
								AsistenID = ".$_POST['ddlAsisten'.$j].",
								TerapisID = ".$_POST['ddlTerapis'.$j].",
								LayananID = ".$_POST['ddlLayanan'.$j].",
								Harga = ".str_replace(",", "", $_POST['txtHarga'.$j]).",
								Harga_Layanan = ".$_POST['hdnHargaLayanan'.$j].",
								FeeKonsultan = ".$_POST['hdnFeeKonsultan'.$j].",
								Jumlah = ".$_POST['txtJumlah'.$j].",
								Harga_PiketAsisten = ".$ASISTANT_CLIENT_PICKET.",
								ModifiedBy = '".$_SESSION['Username']."'
							WHERE
								DetailID = ".$_POST['hdnDetailID'.$j];
					}
					else {
						if($_POST['ddlProses'.$j] == 2) $HargaKilat = $_POST['hdnStatusProcess'];
						else $HargaKilat = 0;
						$sql = "UPDATE 
								transaksi_rincicustomerservice
							SET
								KonsultanID = ".$_POST['ddlKonsultan'.$j].",
								AsistenID = ".$_POST['ddlAsisten'.$j].",
								LayananID = ".$_POST['ddlLayanan'.$j].",
								Nama = '".$_POST['txtNama'.$j]."',
								Pendidikan = ".$_POST['ddlPendidikan'.$j].",
								NoPsikogram = '".$_POST['txtPsikogram'.$j]."',
								Harga = ".str_replace(",", "", $_POST['txtHarga'.$j]).",
								Harga_Layanan = ".$_POST['hdnHargaLayanan'.$j].",
								FeeKonsultan = ".$_POST['hdnFeeKonsultan'.$j].",
								Jumlah = ".$_POST['txtJumlah'.$j].",
								ModifiedBy = '".$_SESSION['Username']."',
								StatusProcess = ".$_POST['ddlProses'.$j].",
								Harga_StatusProcess = ".$HargaKilat.",
								Harga_PiketAsisten = ".$ASISTANT_CLIENT_PICKET."
							WHERE
								DetailID = ".$_POST['hdnDetailID'.$j];
					}
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
