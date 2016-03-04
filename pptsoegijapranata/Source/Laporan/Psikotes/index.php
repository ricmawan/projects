<?php
	$RequestPath = "$_SERVER[REQUEST_URI]";
	include "../../GetPermission.php";
	//echo $_SERVER['REQUEST_URI'];
	$Content = "";
	$EditFlag = "";
	$DeleteFlag = "";
	if($cek==0) {
		$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
	}
	else {
		//$Content = "Place the content here";
		$row = mysql_fetch_row($result);
		$EditFlag = $row[1];
		$DeleteFlag = $row[2];
		$TableColumn = "<td>No</td><td>Konsultan</td><td>Cakar</td>";
		$TableBody = "";
		if(isset($_POST['hdnPostBack'])) {
			$MonthYear = explode("-", $_POST['ddlMonthYear']);
			$Month = $MonthYear[0];
			$Year = $MonthYear[1];
		}
		else {
			$Month = date('n');
			$Year = date('Y');
		}
		$sql = "SELECT 
					FI.FileID,
					K.KlienID,
					K.Nama,
					KN.KonsultanID,
					KN.Nama,
					DATE_FORMAT(FI.Tanggal, '%d-%m-%Y'),
					FI.FileName,
					FI.FilePath,
					FI.Extension
				FROM 
					report_fileinfo FI
					JOIN master_klien K
						ON FI.KlienID = K.KlienID
					JOIN master_konsultan KN
						ON FI.KonsultanID = KN.KonsultanID";
		if (! $result=mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		$RowNumber = 0;
		while ($row=mysql_fetch_row($result)) {
			$RowNumber ++;
			$Content .= "<tr>";
			if($DeleteFlag == true) $Content .= "<td><input type='checkbox' name='DeleteID' class='delete' value='".$row[0]."' /></td>";
			$Content .= "
							<td>$RowNumber</td>
							<td>$row[0]</td>
							<td>$row[2]</td>
							<td>$row[4]</td>
							<td>$row[5]</td>
							<td><a download href='".$row[7].$row[6]."' acronym title='Klik Untuk Download File'>$row[6]</a></td>
						";
			if($EditFlag == true) {
				//$Content .='<td><i class="fa fa-edit menu" link="./Master/Layanan/Detail.php?id='.$row[0].'" acronym title="Ubah Data"></i>&nbsp;</td>';
			}
			$Content .= "</tr>";
		}
		echo "<input type='hidden' id='hdnMonthYear' value='".$Month."-".$Year."' />";
	}
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<h2>Laporan Psikotest</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<?php
					if($cek == 0) echo $Content;
					else {
				?>
						<div class="panel panel-default">
							<div class="panel-heading">
								 Laporan Psikotest
							</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover" id="DataTable">
										<thead>				
											<tr>
												<?php if($DeleteFlag == true) { echo "<th><input type='checkbox' name='check-all' id='check-all' onclick='checkall();' /></th>"; } ?>
												<th>No</th>
												<th>ID File</th>
												<th>Klien</td>
												<th>Konsultan</th>
												<th>Tanggal</th>
												<th>Nama File</th>
												<?php //if($EditFlag == true) { echo "<th>Opsi</td>"; } ?>
											</tr>
										</thead>
										<tbody>
											<?php echo $Content; ?>
										</tbody>
										
									</table>
									<button class="btn btn-primary menu" link="./Laporan/Psikotes/Detail.php?id=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;<button class="btn btn-danger" onclick="DeleteData('./Laporan/Psikotes/Delete.php');" ><i class="fa fa-close"></i> Hapus</button>
								</div>
							</div>
						</div>
				<?php
					}
				?>
			</div>
		</div>
		<script>
			 $(document).ready(function () {
                $('#DataTable').dataTable();
            });
		</script>
	</body>
</html>