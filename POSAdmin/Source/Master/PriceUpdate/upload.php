<?php
   // Edit upload location here
	$RequestedPath = "$_SERVER[REQUEST_URI]";
	$file = basename($RequestedPath);
	$RequestedPath = str_replace($file, "", $RequestedPath);
	include "../../GetPermission.php";
	require_once '../../assets/lib/PHPExcel.php';
	require_once '../../assets/lib/PHPExcel/IOFactory.php';
	$destination_path = "../../assets/uploadedFiles/";

	$success = 0;
	$message = "";

	$temp = explode(".", $_FILES["myfile"]["name"]);
	$newfilename = round(microtime(true)) . '.' . end($temp);
	$target_path = $destination_path . $newfilename;

	if(@move_uploaded_file($_FILES['myfile']['tmp_name'], $target_path)) {
		$success = 1;
	}
	else {
		$message = "Terjadi error saat proses upload!";
		echo '<script language="javascript" type="text/javascript">window.top.window.stopUpload('.$success.', "'. $message.'")</script>';
		return false;
	}

	$load = PHPExcel_IOFactory::load($target_path);
	$sheets = $load->getActiveSheet()->toArray(null,true,true,true);
	$table = "";
	$numrow = 1;
	$remarks = "";
	foreach($sheets as $rows) {
		if($numrow > 1 && !empty($rows['A'])) {
			$ItemID = $rows['A'];
			$BuyPrice = str_replace(",", "", $rows['E']);
			$RetailPrice = str_replace(",", "", $rows['F']);
			$Price1 = str_replace(",", "", $rows['G']);
			$Qty1 = str_replace(",", "", $rows['H']);
			$Price2 = str_replace(",", "", $rows['I']);
			$Qty2 = str_replace(",", "", $rows['J']);

			if (empty($BuyPrice) OR empty($RetailPrice) OR empty($Price2) OR empty($Price1) OR empty($Qty1) OR empty($Qty1)) {
				$remarks = "Terdapat kolom yang kosong";
			}
			else if(!is_numeric($BuyPrice) OR !is_numeric($RetailPrice) OR !is_numeric($Price2) OR !is_numeric($Price1) OR !is_numeric($Qty1) OR !is_numeric($Qty1) ) {
				$remarks = "Mohon input angka pada kolom harga & qty!";
			}

			else {
				$sql = "CALL spUpdItem('".$ItemID."',
									".$BuyPrice.",
									".$RetailPrice.",
									".$Price1.",
									".$Qty1.",
									".$Price2.",
									".$Qty2.",
									'".$_SESSION['UserLogin']."'
								  )";

				if (!$result = mysqli_query($dbh, $sql)) {
					logEvent(mysqli_error($dbh), '/Master/UpdatePrice/upload.php', mysqli_real_escape_string($dbh, $_SESSION['UserLogin']));
				}

				$row = mysqli_fetch_array($result);
				mysqli_free_result($result);
				mysqli_next_result($dbh);
				$remarks = $row['Message'];
			}

			$table .= "<tr>";
			$table .= "<td>".$ItemID."</td>";
			$table .= "<td>".$rows['B']."</td>";
			$table .= "<td>".$rows['C']."</td>";
			$table .= "<td>".$rows['D']."</td>";
			$table .= "<td>".$rows['E']."</td>";
			$table .= "<td>".$rows['F']."</td>";
			$table .= "<td>".$rows['G']."</td>";
			$table .= "<td>".$rows['H']."</td>";
			$table .= "<td>".$rows['I']."</td>";
			$table .= "<td>".$rows['J']."</td>";
			$table .= "<td>".$remarks."</td>";
			$table .= "</tr>";
		}
		$numrow++;
	}

	sleep(1);
?>

<script language="javascript" type="text/javascript">window.top.window.stopUpload(<?php echo $success.", '". $message."', '". $table."'"; ?>);</script>
