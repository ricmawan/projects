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
					$sql = "SELECT FilePath, FileName FROM report_fileinfo WHERE FileID = $data[$i]";
					if (! $result=mysql_query($sql, $dbh)) {
						echo mysql_error();
						return 0;
					}
					$row = mysql_fetch_row($result);
					unlink("../../UploadedFiles/$row[1]"); 
					$sql = "DELETE FROM report_fileinfo WHERE FileID = $data[$i]";
					if (! $result=mysql_query($sql, $dbh)) {
						throw new Exception($username);
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
				
			if($messagesuccessdelete !="") $messagesuccess = "ID File " .$messagesuccessdelete. " Berhasil Dihapus";
			else $messagesuccess = "";
			if($messagefaileddelete !="") $messagefailed = "ID File " .$messagefaileddelete. " Gagal Dihapus";
			else $messagefailed = "";
			
			echo "$messagesuccess+$messagefailed";
		}
	}
?>