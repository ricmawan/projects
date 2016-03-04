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
		//$Content = "Place the content here";
		$sql = "SELECT
				TK.TransaksiID,
				DATE_FORMAT(TK.Tanggal, '%d-%m-%Y')
			FROM
				transaksi_kas TK";
					
		if (! $result=mysql_query($sql, $dbh)) {
			echo mysql_error();
			return 0;
		}
		
		$RowNumber = 0;
		while ($row=mysql_fetch_row($result)) {
			$RowNumber++;
			$Content .= "<tr>";
			if($DeleteFlag == true) $Content .= "<td><input type='checkbox' name='DeleteID' class='delete' value='".$row[0]."' /></td>";
			$Content .= "
							<td>$RowNumber</td>
							<td>$row[0]</td>
							<td>$row[1]</td>
						";
			if($EditFlag == true) {
				$Content .='<td><i class="fa fa-edit menu" link="./Keuangan/Kas/Detail.php?id='.$row[0].'" acronym title="Ubah Data"></i>&nbsp;</td>';
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
				<h2>Kas Keluar</h2>   
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
								Kas Keluar
							</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover" id="DataTable">
										<thead>				
											<tr>
												<?php if($DeleteFlag == true) { echo "<th><input type='checkbox' name='check-all' id='check-all' onclick='checkall();' /></th>"; } ?>
												<th>No</th>
												<th>ID Transaksi</th>
												<th>Tanggal</th>
												<?php if($EditFlag == true) { echo "<th>Opsi</th>"; } ?>
											</tr>
										</thead>
										<tbody>
											<?php echo $Content; ?>
										</tbody>
										
									</table>
									<button class="btn btn-primary menu" link="./Keuangan/Kas/Detail.php?id=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;<button class="btn btn-danger" onclick="DeleteData('./Keuangan/Kas/Delete.php');" ><i class="fa fa-close"></i> Hapus</button>
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
