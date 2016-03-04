<?php
	if(isset($_POST['hdnId'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$Id = $_POST['hdnId'];
		$KlienID = $_POST['ddlKlien'];
		$KonsultanID = $_POST['ddlKonsultan'];
		$txtTanggal = explode('-', $_POST['txtTanggal']);
		$_POST['txtTanggal'] = "$txtTanggal[2]-$txtTanggal[1]-$txtTanggal[0]"; 
		$txtTanggal = $_POST['txtTanggal'];
		$Message = "Data Berhasil Disimpan";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {
			IF(ISSET($_FILES['uploadfile'])) {
				$filename = strtolower($_FILES['uploadfile']['name']);
				$exts = explode(".", $filename) ; 
				$n = count($exts)-1; 
				$exts = $exts[$n]; 
				$date = date("dmYHis");
				$new_file_name="Psikotes ".$date." ".$filename;

				//set where you want to store files
				$path= "../../UploadedFiles/".$new_file_name;
				if(move_uploaded_file($_FILES['uploadfile']['tmp_name'], $path)) 
				{
					//echo "The file has been uploaded as ".$path;
					$sql=	"INSERT INTO report_fileinfo
							 (
								FileID,
								KlienID,
								KonsultanID,
								FileName,
								FilePath,
								Extension,
								Tanggal,
								CreatedDate,
								CreatedBy
							)
							VALUES
							(
								0,
								".$KlienID.",
								".$KonsultanID.",
								'".$new_file_name."',
								'".$APPLICATION_PATH."UploadedFiles/',
								'".$exts."',
								'".$txtTanggal."',
								NOW(),
								'".$_SESSION['Username']."'								
							)";
					if (! $result = mysql_query($sql, $dbh)) {
						$Message = "Terjadi Kesalahan Sistem";
						$MessageDetail = mysql_error();
						$FailedFlag = 1;
						echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
						return 0;					
					}
				}
				else
				{
					$Message = "File Gagal disimpan";
					$FailedFlag = 1;
					echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
					return 0;
				}
			}
			echo returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State);
		}
	}
	
	function returnstate($Id, $Message, $MessageDetail, $FailedFlag, $State) {
		$data = $Id."|".$Message."|".$MessageDetail."|".$FailedFlag."|".$State;
		return $data;
	}
?>