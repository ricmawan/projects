<?php
	if(isset($_GET['id'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$Id = $_GET['id'];
		$Keterangan = "";
		$Tipe = "";
		$Harga = "0.00";
		$Satuan="";
		$Jenis="";
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
							master_jobdesk
						WHERE
							jobdeskID = $Id";
							
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_row($result);
				$Id = $row[0];
				$Keterangan = $row[1];
				$Tipe = $row[2];
				$Jenis = $row[3];
				$Satuan = $row[4];
				$Harga = number_format($row[5],2,",",".");
			
				
				
				
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
				<h2>Master Data Jobdesk Asisten</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Data Jobdesk Asisten</strong>  
					</div>
					<div class="panel-body">
						<form class="col-md-6" id="PostForm" method="POST" action="" >
							Keterangan:<br />
							<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="hdnTipe" name="hdnTipe" type="hidden" <?php echo 'value="'.$Tipe.'"'; ?> />
							<input id="hdnSatuan" name="hdnSatuan" type="hidden" <?php echo 'value="'.$Satuan.'"'; ?> />
							<input id="hdnJenis" name="hdnJenis" type="hidden" <?php echo 'value="'.$Jenis.'"'; ?> />
							<input id="txtKeterangan" name="txtKeterangan" type="text" class="form-control" placeholder="Keterangan " required <?php echo 'value="'.$Keterangan.'"'; ?> />
							<br />
							
							Tipe:<br />
							<select class="form-control" name="ddlTipe" id="ddlTipe" required>
								<option value="" selected>-- Pilih Tipe --</option>
								<option value="1">S1 Industri</option>
								<option value="2">S1 Anak&Remaja </option>
								<option value="3">S2 Industri</option>
								
							</select>
							<br />
							
							Satuan:<br />
							<select class="form-control" name="ddlSatuan" id="ddlSatuan" required>
								<option value="" selected>-- Pilih Satuan --</option>
								<option value="1">Per Jaga</option>
								<option value="2">Per Jam</option>
								<option value="3">Per Piket</option>
								<option value="4">Per Klien</option>
								
							</select>
							<br />
							
							Jenis:<br />
							<select class="form-control" name="ddlJenis" id="ddlJenis" required>
								<option value="" selected>-- Pilih Jenis --</option>
								<option value="1">Reguler</option>
								<option value="2">Job Luar </option>
								
							</select>
							<br />
							
							Harga:<br />
							<input id="txtHarga" name="txtHarga" type="text"  class="form-control"  placeholder="Harga" onfocus="clearFormat(this.id, this.value)" onkeypress="return isNumberKey(event, this.id, this.value)" onblur="convertRupiah(this.id, this.value)"  required <?php echo 'value="'.$Harga.'"'; ?> />
							<br />
							
							
							<br />
							
							<input type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/JobdeskAsisten/Insert.php');" />
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
		
			$(document).ready(function () {
				$("#ddlTipe").val($("#hdnTipe").val());
				$("#ddlSatuan").val($("#hdnSatuan").val());
				$("#ddlJenis").val($("#hdnJenis").val());
			});
		
		</script>
	</body>
</html>
