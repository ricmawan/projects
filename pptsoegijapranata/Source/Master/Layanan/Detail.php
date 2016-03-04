<?php
	if(isset($_GET['id'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$Id = $_GET['id'];
		$Kode = "";
		$Jenis = "";
		$Harga = "0.00";
		$Satuan = "";
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
							master_layanan
						WHERE
							layananID = $Id";
							
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_row($result);
				$Id = $row[0];
				$Kode = $row[1];
				$Jenis = $row[2];
				$Harga = number_format($row[3],2,".",",");
				$Satuan = $row[4];
				
				
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
				<h2>Master Data layanan </h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Data layanan </strong>  
					</div>
					<div class="panel-body">
						<form class="col-md-6" id="PostForm" method="POST" action="" >
							Kode:<br />
							<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="txtKode" name="txtKode"   type="text" class="form-control" placeholder="Kode " required <?php echo 'value="'.$Kode.'"'; ?> />
							<input id="hdnSatuan" name="hdnSatuan" type="hidden" <?php echo 'value="'.$Satuan.'"'; ?> />
							<br />
							
							Jenis:<br />
							<input id="txtJenis" name="txtJenis" type="text" class="form-control" placeholder="Jenis" required <?php echo 'value="'.$Jenis.'"'; ?> />
							<br />
							
							Harga:<br />
							<input id="txtHarga" name="txtHarga" type="text" class="form-control" placeholder="Harga" onfocus="clearFormat(this.id, this.value)" onkeypress="return isNumberKey(event, this.id, this.value)" onblur="convertRupiah(this.id, this.value)" required <?php echo 'value="'.$Harga.'"'; ?> />
							<br />
							
							Satuan:<br />
							<select class="form-control" name="ddlSatuan" id="ddlSatuan" required>
								<option value="" selected>-- Pilih Satuan --</option>
								<option value="1">Per Orang/Anak</option>
								<option value="2">Per Jam</option>
								
							</select>
							<br />
							
							<input type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/Layanan/Insert.php');" />
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
		
			$(document).ready(function () {
				$("#ddlSatuan").val($("#hdnSatuan").val());
			});
		
		</script>
	</body>
</html>