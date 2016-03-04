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
					*
				FROM
					master_klien
				where 
					jenisKlien = 3";
					
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
							<td>$row[2]</td>
							<td>$row[7]</td>
							<td>$row[8]</td>
							<td>$row[9]</td>
							<td>$row[10]</td>
							<td>$row[11]</td>
							<td>$row[12]</td>
							<td>$row[13]</td>
							<td>$row[14]</td>
							
						";
			if($EditFlag == true) {
				$Content .='<td><i class="fa fa-edit menu" link="./Klien/Pendidikan/Detail.php?id='.$row[0].'" acronym title="Ubah Data"></i>&nbsp;</td>';
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
				<h2>Master Data Klien Pendidikan</h2>   
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
								 Master Data Klien Pendidikan
							</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover" id="DataTable">
										<thead>				
											<tr>
												<?php if($DeleteFlag == true) { echo "<th><input type='checkbox' name='check-all' id='check-all' onclick='checkall();' /></th>"; } ?>
												<th>No</th>
												<th>Nama</th>
												<th>Alamat</th>
												<th>Telepon</th>
												<th>Fax</th>
												<th>Hp</th>
												<th>CP</th>
												<th>Nama Bank</th>
												<th>No Rekening</th>												
												<th>Keterangan</th>
												<?php if($EditFlag == true) { echo "<th>Opsi</td>"; } ?>
											</tr>
										</thead>
										<tbody>
											<?php echo $Content; ?>
										</tbody>
										
									</table>
									<button class="btn btn-primary menu" link="./Klien/Pendidikan/Detail.php?id=0"><i class="fa fa-plus "></i> Tambah</button>&nbsp;<button class="btn btn-danger" onclick="DeleteData('./Klien/Pendidikan/Delete.php');" ><i class="fa fa-close"></i> Hapus</button>
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