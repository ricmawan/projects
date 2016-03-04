<?php
	if(isset($_POST['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$messagesuccessdelete = "";
		$messagefaileddelete = "";
		
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {		
			$data = $_POST['ID'];
			
			for($i=0; $i<count($data); $i++) {
				try
				{
					$user = explode("^", $data[$i]);
					$username = $user[1];
					$iduser = $user[0];
					$sql = "DELETE FROM master_klien WHERE KlienID = $iduser";
					if (! $result=mysql_query($sql, $dbh)) {
						throw new Exception($username);
					}
					$messagesuccessdelete .= "$username, ";
				}
				catch (Exception $e)
				{
					$messagefaileddelete .= $e->getMessage() .", ";
				}
			}
			$messagesuccessdelete = substr($messagesuccessdelete, 0, -2);
			$messagefaileddelete = substr($messagefaileddelete, 0, -2);
				
			if($messagesuccessdelete !="") $messagesuccess = "Klien " .$messagesuccessdelete. " Berhasil Dihapus";
			else $messagesuccess = "";
			if($messagefaileddelete !="") $messagefailed = "Klien " .$messagefaileddelete. " Gagal Dihapus";
			else $messagefailed = "";
			
			echo "$messagesuccess+$messagefailed";
		}
	}
?>