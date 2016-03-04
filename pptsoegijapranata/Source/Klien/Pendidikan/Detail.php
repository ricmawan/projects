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
		$Fax = "";
		$Hp = "";
		$Cp = "";
		$NamaBank = "";
		$Norek = "";
		$Keterangan = "";
		$jenisKlien =3;
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
							master_klien
						WHERE
							klienID = $Id
						AND
							jenisKlien = 3";
							
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_row($result);
				$Id = $row[0];
				$jenisKlien = $row[1];
				$Nama = $row[2];
				$Alamat = $row[7];
				$Telepon = $row[8];
				$Fax = $row[9];
				$Hp = $row[10];
				$Cp = $row[11];
				$NamaBank = $row[12];
				$Norek = $row[13];
				$Keterangan = $row[14];
				
				
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
				<h2>Master Data Klien Pendidikan</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Data Klien Pendidikan</strong>  
					</div>
					<div class="panel-body">
						<form class="col-md-6" id="PostForm" method="POST" action="" >
							Nama:<br />
							<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="txtNama" name="txtNama" type="text" class="form-control" placeholder="Nama " required <?php echo 'value="'.$Nama.'"'; ?> />
							<input id="hdnJenis" name="hdnJenis" type="hidden"  <?php echo 'value="'.$jenisKlien.'"'; ?> />
							
							<br />
							
							Alamat:<br />
							<input id="txtAlamat" name="txtAlamat" type="text" class="form-control" placeholder="Alamat" required <?php echo 'value="'.$Alamat.'"'; ?> />
							<br />
							
							Telepon:<br />
							<input id="txtTelepon" name="txtTelepon" type="text" class="form-control" placeholder="Telepon" onkeypress="return isNumberKey(event, this.id, this.value)" required <?php echo 'value="'.$Telepon.'"'; ?> />
							<br />
							
							Fax:<br />
							<input id="txtFax" name="txtFax" type="text" class="form-control" placeholder="Fax" onkeypress="return isNumberKey(event, this.id, this.value)" required <?php echo 'value="'.$Fax.'"'; ?> />
							<br />
							
							HP:<br />
							<input id="txtHP" name="txtHP" type="text" class="form-control" placeholder="HP" onkeypress="return isNumberKey(event, this.id, this.value)" required <?php echo 'value="'.$Hp.'"'; ?> />
							<br />
							
							CP:<br />
							<input id="txtCP" name="txtCP" type="text" class="form-control" placeholder="CP"  required <?php echo 'value="'.$Cp.'"'; ?> />
							<br />
							
							Nama Bank:<br />
							<input id="txtBank" name="txtBank" type="text" class="form-control" placeholder="Bank"  required <?php echo 'value="'.$NamaBank.'"'; ?> />
							<br />
							
							No Rek:<br />
							<input id="txtRek" name="txtRek" type="text" class="form-control" placeholder="Rek" onkeypress="return isNumberKey(event, this.id, this.value)" required <?php echo 'value="'.$Norek.'"'; ?> />
							<br />
							
							Keterangan:<br />
							<input id="txtKeterangan" name="txtKeterangan" type="text" class="form-control" placeholder="Keterangan"  required <?php echo 'value="'.$Keterangan.'"'; ?> />
							<br />
							<input type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Klien/Pendidikan/Insert.php');" />
						</form>
					</div>
				</div>
			</div>
		</div>
		
	</body>
</html>