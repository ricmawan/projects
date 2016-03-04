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
		
		$sql = "SELECT
				MA.AlatID,
				MA.NamaAlat,
				SUM(IFNULL(RIM.Jumlah, 0)) - (SELECT IFNULL(SUM(RI.Jumlah), 0) FROM transaksi_rinciinventaris RI WHERE RI.AlatID = MA.AlatID),
				CASE
					WHEN MA.Satuan= 1
					THEN 'Pcs'
					WHEN MA.Satuan = 2
					THEN 'Pax'
				END AS Satuan
			FROM
				master_alat MA
				LEFT JOIN transaksi_rinciinventarismasuk RIM
					ON MA.AlatID = RIM.AlatID
			GROUP BY
				MA.AlatID";

					
		if (! $result=mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		
		$RowNumber = 0;
		while ($row=mysql_fetch_row($result)) {
			$RowNumber++;
			$Content .= "<tr>";
		
			if($DeleteFlag == true) $Content .= "<td><input type='checkbox' name='DeleteID' class='delete' value='".$row[0]."^".$row[1]."' /></td>";
			$Content .= "
							<td>$RowNumber</td>
							<td>$row[1]</td>
							<td>$row[2]</td>
							<td>$row[3]</td>
							
							
						";
			if($EditFlag == true) {
				$Content .='<td><i class="fa fa-edit menu" link="./Master/Alat/Detail.php?id='.$row[0].'" acronym title="Ubah Data"></i>&nbsp;</td>';
			}
			$Content .= "</tr>";
		}
		
	}
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<h2>Master Data Alat</h2>   
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
								 Master Data Alat
							</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover" id="DataTable">
										<thead>				
											<tr>
												<?php if($DeleteFlag == true) { echo "<th><input type='checkbox' name='check-all' id='check-all' onclick='checkall();' /></th>"; } ?>
												<th>No</th>
												<th>Nama Alat</th>
												<th>Jumlah</th>
												<th>Satuan</th>
												<?php if($EditFlag == true) { echo "<th>Opsi</td>"; } ?>
											</tr>
										</thead>
										<tbody>
											<?php echo $Content; ?>
										</tbody>
										
									</table>
									<button class="btn btn-primary menu" link="./Master/Alat/Detail.php?id=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;<button class="btn btn-danger" onclick="DeleteData('./Master/Alat/Delete.php');" ><i class="fa fa-close"></i> Hapus</button>
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
