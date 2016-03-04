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
					$sql = "DELETE FROM transaksi_rincifee WHERE TransaksiID = $data[$i]";
					if (! $result=mysql_query($sql, $dbh)) {
						throw new Exception($data[$i]);
					}
					$sql = "DELETE FROM transaksi_fee WHERE TransaksiID = $data[$i]";
					if (! $result=mysql_query($sql, $dbh)) {
						throw new Exception($data[$i]);
					}
					$messagesuccessdelete .= "$data[$i], ";
				}
				catch (Exception $e)
				{
					$messagefaileddelete .= $e->getMessage() .", ";
				}
			}
			$messagesuccessdelete = substr($messagesuccessdelete, 0, -2);
			$messagefaileddelete = substr($messagefaileddelete, 0, -2);
				
			if($messagesuccessdelete !="") $messagesuccess = "Id Transaksi " .$messagesuccessdelete. " Berhasil Dihapus";
			else $messagesuccess = "";
			if($messagefaileddelete !="") $messagefailed = "Id Transaksi " .$messagefaileddelete. " Gagal Dihapus";
			else $messagefailed = "";
			
			echo "$messagesuccess+$messagefailed";
		}
	}
?>
