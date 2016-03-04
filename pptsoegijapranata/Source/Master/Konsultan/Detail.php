<?php
	if(isset($_GET['id'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$Id = $_GET['id'];
		$Nama = "";
		$Alamat = "";
		$Telepon = "";
		$Pph ="";
		$IsEdit = 0;
		
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {
			if($Id !=0) {
				$IsEdit = 1;
				//$Content = "Place the content here";
				$sql = "SELECT
							*
						FROM
							master_konsultan
						WHERE
							
							KonsultanID = '$Id'";
							
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_row($result);
				$Id = $row[0];
				$Nama = $row[1];
				$Alamat = $row[2];
				$Telepon = $row[3];
				$Pph = $row[4];
				
				
				
			}
		}
	}
?>
<html>
	<head>

	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<h2>Master Data konsultan</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Data konsultan</strong>  
					</div>
					<div class="panel-body">
						<form class="col-md-6" id="PostForm" method="POST" action="" >
							Nama:<br />
							<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="txtNama" name="txtNama" type="text" class="form-control"  placeholder="Nama " required <?php echo 'value="'.$Nama.'"'; ?> />						
							<br />
							
							Alamat:<br />
							<input id="txtAlamat" name="txtAlamat" type="text" class="form-control"  placeholder="Alamat" required <?php echo 'value="'.$Alamat.'"'; ?> />
							<br />
							
							Telepon:<br />
							<input id="txtTelepon" name="txtTelepon" type="text" class="form-control"  placeholder="Telepon" onkeypress="return isNumberKey(event, this.id, this.value)" required <?php echo 'value="'.$Telepon.'"'; ?> />
							<br />
							
							Pph (%):<br />
							<input id="txtPph" name="txtPph" type="text" class="form-control" placeholder="Pph"  onkeypress="return isNumberKey(event, this.id, this.value)" onkeyup="this.value = minmax(this.value, 0, 100)" required <?php echo 'value="'.$Pph.'"'; ?> />
							<br />
						
							
							<input type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/Konsultan/Insert.php');" />
						</form>
					</div>
				</div>
			</div>
		</div>
		
	</body>
</html>
