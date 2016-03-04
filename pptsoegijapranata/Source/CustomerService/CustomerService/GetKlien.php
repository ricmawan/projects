<?php
	if(isset($_POST['ddlJenis'])) {	
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";	
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {
			$IdJenis = $_POST['ddlJenis'];
			$sql = "SELECT * FROM master_klien WHERE JenisKlien = ".$IdJenis;
			if(!$result = mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}
			while($row = mysql_fetch_row($result)) {
				echo "<option value='".$row[0]."' cp='".$row[11]."'>".$row[2]."</option>";
			}
		}
	}
?>
