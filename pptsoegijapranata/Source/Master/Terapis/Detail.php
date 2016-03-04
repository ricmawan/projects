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
		$Alamat ="";
		$Telepon ="";
		$Prognosis="";
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
							master_terapis
						WHERE
							TerapisID = $Id";
							
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_row($result);
				$Id = $row[0];
				$Nama = $row[1];
				$Alamat = $row[2];
				$Telepon= $row[3];
				$Prognosis = $row[4];
				
				
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
				<h2>Master Data Terapis</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Data Terapis</strong>  
					</div>
					<div class="panel-body">
						<form class="col-md-6" id="PostForm" method="POST" action="" >
							Nama:<br />
							<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="txtNama" name="txtNama" type="text" class="form-control" placeholder="Nama "    required <?php echo 'value="'.$Nama.'"'; ?> />
							<br />
							
							Alamat:<br />
							<input id="txtAlamat" name="txtAlamat" type="text" class="form-control" placeholder="Alamat"    required <?php echo 'value="'.$Alamat.'"'; ?> />
							<br />
							
							Telepon:<br />
							<input id="txtTelepon" name="txtTelepon" type="text" class="form-control" placeholder="Telepon"    onkeypress="return isNumberKey(event, this.id, this.value)" required <?php echo 'value="'.$Telepon.'"'; ?> />
							<br />
							
							Prognosis:<br />
							<input id="txtPrognosis" name="txtPrognosis" type="text" class="form-control" placeholder="Prognosis"    required <?php echo 'value="'.$Prognosis.'"'; ?> />
							<br />
							
							<input type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/Terapis/Insert.php');" />
						</form>
					</div>
				</div>
			</div>
		</div>
		
	</body>
</html>