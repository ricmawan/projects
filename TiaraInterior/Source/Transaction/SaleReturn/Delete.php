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
				$SaleReturnData = explode("^", mysql_real_escape_string($Data[$i]));
				$SaleReturnID = $SaleReturnData[0];
				$SaleReturnNumber = $SaleReturnData[1];
				$sql = "DELETE FROM transaction_salereturn WHERE SaleReturnID = '".$SaleReturnID."'";
				if (! $result=mysql_query($sql, $dbh)) {
					throw new Exception($SaleReturnNumber);
				}
				$MessageSuccessDelete .= "$SaleReturnNumber, ";
			}
			catch (Exception $e)
			{
				$MessageFailedDelete .= $e->getMessage() .", ";
			}
		}
		$MessageSuccessDelete = substr($MessageSuccessDelete, 0, -2);
		$MessageFailedDelete = substr($MessageFailedDelete, 0, -2);
			
		if($MessageSuccessDelete !="") $MessageSuccess = "No Nota " .$MessageSuccessDelete. " Berhasil Dihapus";
		else $MessageSuccess = "";
		if($MessageFailedDelete !="") $MessageFailed = "No Nota " .$MessageFailedDelete. " Gagal Dihapus";
		else $MessageFailed = "";
		
		echo "$MessageSuccess+$MessageFailed";
	}
?>
