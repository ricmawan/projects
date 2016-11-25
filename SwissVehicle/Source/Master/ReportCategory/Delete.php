<?php
	if(isset($_POST['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$MessageSuccessDelete = "";
		$MessageFailedDelete = "";
	
		$Data = $_POST['ID'];
		
		for($i=0; $i<count($Data); $i++) {
			try
			{
				$ReportCategoryData = explode("^", $Data[$i]);
				$ReportCategoryID = mysql_real_escape_string($ReportCategoryData[0]);
				$ReportCategoryName = $ReportCategoryData[1];
				$sql = "DELETE FROM master_reportcategory WHERE ReportCategoryID = $ReportCategoryID";
				if (! $result=mysql_query($sql, $dbh)) {
					throw new Exception($ReportCategoryName);
				}
				$MessageSuccessDelete .= "$ReportCategoryName, ";
			}
			catch (Exception $e)
			{
				$MessageFailedDelete .= $e->getMessage() .", ";
			}
		}
		$MessageSuccessDelete = substr($MessageSuccessDelete, 0, -2);
		$MessageFailedDelete = substr($MessageFailedDelete, 0, -2);
			
		if($MessageSuccessDelete !="") $MessageSuccess = "Kategori Laporan " .$MessageSuccessDelete. " Berhasil Dihapus";
		else $MessageSuccess = "";
		if($MessageFailedDelete !="") $MessageFailed = "Kategori Laporan " .$MessageFailedDelete. " Gagal Dihapus";
		else $MessageFailed = "";
		
		echo "$MessageSuccess+$MessageFailed";
	}
?>
