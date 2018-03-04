<?php
	if(isset($_POST['SessionID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$SessionID = mysql_real_escape_string($_POST['SessionID']);
		$EditFlag = mysql_real_escape_string($_POST['EditFlag']);
		$Message = "";
		$MaterialDetails = "";
		$MessageDetail = "";
		$FailedFlag = 0;
		$State = 1;
		$Total = 0;
		$sql = "SELECT
					TMD.MaterialDetailsID,
					MM.MaterialName,
					TMD.Quantity,
					TMD.SalePrice,
					(TMD.SalePrice * TMD.Quantity) Total,
					TMD.Remarks,
					TMD.SessionID
				FROM
					transaction_materialdetails TMD
					JOIN master_material MM
						ON MM.MaterialID = TMD.MaterialID
				WHERE
					TMD.SessionID = '$SessionID'
				ORDER BY
					TMD.CreatedDate";
					
		if (! $result = mysql_query($sql, $dbh)) {
			$Message = "Terjadi Kesalahan Sistem";
			$MessageDetail = mysql_error();
			$FailedFlag = 1;
			echo returnstate($SessionID, $Message, $MessageDetail, $MaterialDetails, $FailedFlag, $State, $Total);
			return 0;
		}
		
		$RowNumber = 1;
		while ($row = mysql_fetch_array($result)) {
			$MaterialDetails .= "<tr>";
			$MaterialDetails .= "<td align='center' style='width: 36px;' >$RowNumber</td>";
			$MaterialDetails .= "<td align='left' style='width: 200px;' >".$row['MaterialName']."</td>";
			$MaterialDetails .= "<td align='right' style='width: 80px;' >".$row['Quantity']."</td>";
			$MaterialDetails .= "<td align='right' style='width: 100px;' >".number_format($row['SalePrice'],2,".",",")."</td>";
			$MaterialDetails .= "<td align='right' style='width: 125px;' >".number_format($row['Total'],2,".",",")."</td>";
			$MaterialDetails .= "<td align='left' style='width: 210px;' >".$row['Remarks']."</td>";
			$MaterialDetails .= '<td align="center" style="vertical-align:middle;width: 60px;">
										<i style="cursor:pointer;" class="fa fa-edit" onclick="EditMaterial('.$row['MaterialDetailsID'].', '.$row['Quantity'].', \''.number_format($row['SalePrice'],2,".",",").'\', \''.$row['Remarks'].'\', \''.$row['MaterialName'].'\', '.$EditFlag.');" acronym title="Ubah Data"></i>
										&nbsp;&nbsp;<i class="fa fa-close btnDelete" onclick="DeleteMaterial(\''.$SessionID.'\', '.$row['MaterialDetailsID'].', \''.$row['MaterialName'].'\', '.$EditFlag.');" style="cursor:pointer;" acronym title="Hapus Data" ></i>
									</td>';
			$MaterialDetails .= "</tr>";
			$RowNumber++;
			$Total += $row['Total'];
		}
		echo returnstate($SessionID, $Message, $MessageDetail, $MaterialDetails, $FailedFlag, $State, $Total);
		return 0;
	}
	
	function returnstate($ID, $Message, $MessageDetail, $MaterialDetails, $FailedFlag, $State, $Total) {
		$data = array(
			"ID" => $ID, 
			"Message" => $Message,
			"MessageDetail" => $MessageDetail,
			"MaterialDetails" => $MaterialDetails,
			"FailedFlag" => $FailedFlag,
			"State" => $State,
			"Total" => $Total
			
		);
		return json_encode($data);
	
	}
?>