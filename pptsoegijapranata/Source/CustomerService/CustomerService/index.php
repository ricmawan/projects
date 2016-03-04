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
					CS.TransaksiID,
					DATE_FORMAT(CS.Tanggal, '%d-%m-%Y'),
					CASE
						WHEN CS.Jenis = 1
						THEN 'Perusahaan/Industri'
						WHEN CS.Jenis = 2
						THEN 'Anak & Remaja'
						WHEN CS.Jenis = 4
						THEN 'Dewasa'
						WHEN CS.Jenis = 3
						THEN 'Pendidikan'
					END AS Jenis,
					K.Nama,
					K.ContactPerson
				FROM
					transaksi_customerservice CS
					JOIN master_klien K
						ON K.KlienID = CS.KlienID";
					
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
							<td>$row[2]</td>
							<td>$row[3]</td>
							<td>$row[4]</td>
						";
			if($EditFlag == true) {
				$Content .='<td><i class="fa fa-edit menu" link="./CustomerService/CustomerService/Detail.php?id='.$row[0].'" acronym title="Ubah Data"></i>&nbsp;</td>';
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
				<h2>Transaksi</h2>   
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
								 Transaksi Customer Service
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
												<th>Jenis</th>
												<th>Nama Klien</th>
												<th>Contact Person</th>
												<?php if($EditFlag == true) { echo "<th>Opsi</th>"; } ?>
											</tr>
										</thead>
										<tbody>
											<?php echo $Content; ?>
										</tbody>
										
									</table>
									<button class="btn btn-primary menu" link="./CustomerService/CustomerService/Detail.php?id=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;<button class="btn btn-danger" onclick="DeleteData('./CustomerService/CustomerService/Delete.php');" ><i class="fa fa-close"></i> Hapus</button>
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
