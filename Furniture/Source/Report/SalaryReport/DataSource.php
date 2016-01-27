<?php
	if(ISSET($_GET['PeriodID']) && ISSET($_GET['EmployeeID'])) {
		header('Content-Type: application/json');
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$PeriodID = mysql_real_escape_string($_GET['PeriodID']);
		$EmployeeID = mysql_real_escape_string($_GET['EmployeeID']);
		$where = " 1=1 ";
		$order_by = "S.SalaryDate";
		$rows = 10;
		$current = 1;
		$limit_l = ($current * $rows) - ($rows);
		$limit_h = $limit_l + $rows ;
		//Handles Sort querystring sent from Bootgrid
		if (ISSET($_REQUEST['sort']) && is_array($_REQUEST['sort']) )
		{
			$order_by = "";
			foreach($_REQUEST['sort'] as $key => $value) {
				if($key != 'No') $order_by .= " $key $value";
				else $order_by = "DATE_FORMAT(S.SalaryDate, '%d-%m-%Y') ASC";
			}
		}
		//Handles search querystring sent from Bootgrid
		if (ISSET($_REQUEST['searchPhrase']) )
		{
			$search = trim($_REQUEST['searchPhrase']);
			$where .= " AND ( DATE_FORMAT(S.SalaryDate, '%d-%m-%Y') LIKE '%".$search."%' OR P.ProjectName LIKE '%".$search."%'  )";
		}
		//Handles determines where in the paging count this result set falls in
		if (ISSET($_REQUEST['rowCount']) ) $rows = $_REQUEST['rowCount'];
		//calculate the low and high limits for the SQL LIMIT x,y clause
		if (ISSET($_REQUEST['current']) )
		{
			$current = $_REQUEST['current'];
			$limit_l = ($current * $rows) - ($rows);
			$limit_h = $rows ;
		}
		if ($rows == -1) $limit = ""; //no limit
		else $limit = " LIMIT $limit_l, $limit_h ";
		mysql_query("SET @Balance:=0;", $dbh);
		mysql_query("SET @row:=0;", $dbh);
		$sql = "SELECT
					DATE_FORMAT(S.SalaryDate, '%d-%m-%Y') AS SalaryDate,
					SD.DailySalary,
					SD.Days,
					SD.Remarks,
					P.ProjectName,
					(SD.Days * SD.DailySalary) AS Total
				FROM
					transaction_salary S
					JOIN transaction_salarydetails SD
						ON S.SalaryID = SD.SalaryID
					JOIN master_project P
						ON P.ProjectID = SD.ProjectID
				WHERE
					$where
					AND S.PeriodID = ".$PeriodID."
					AND SD.EmployeeID = ".$EmployeeID."
				ORDER BY 
					$order_by";
		
		if (! $result = mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$nRows = mysql_num_rows($result);		
		$return_arr = array();
		$RowNumber = 0;
		//$nRows = mysql_num_rows($result);
		while ($row = mysql_fetch_array($result)) {
			$RowNumber++;
			$row_array['RowNumber'] = $RowNumber;
			$row_array['SalaryDate'] = $row['SalaryDate'];
			$row_array['ProjectName']= $row['ProjectName'];
			$row_array['DailySalary'] = number_format($row['DailySalary'],2,".",",");
			$row_array['Days'] = $row['Days'];
			$row_array['Remarks'] = $row['Remarks'];
			$row_array['Total'] = number_format($row['Total'],2,".",",");
			array_push($return_arr, $row_array);
		}

		$json = json_encode($return_arr);
		echo "{ \"current\": $current, \"rowCount\":$rows, \"rows\": ".$json.", \"total\": $nRows }";
	}
?>
