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
		$Jumlah = "";
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
							master_alat
						WHERE
							AlatID = $Id";
							
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_row($result);
				$Id = $row[0];
				$Nama = $row[1];
				$Jumlah = $row[2];
				$Satuan= $row[3];
				
				
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
				<h2>Master Data Alat</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Data Alat</strong>  
					</div>
					<div class="panel-body">
						<form class="col-md-6" id="PostForm" method="POST" action="" >
							Nama:<br />
							<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="txtNama" name="txtNama" type="text" class="form-control" placeholder="Nama" required <?php echo 'value="'.$Nama.'"'; ?> />
							<input id="hdnSatuan" name="hdnSatuan" type="hidden" <?php echo 'value="'.$Satuan.'"'; ?> />
							<br />
							
							<!--Jumlah:<br />-->
							<input id="txtJumlah" name="txtJumlah" type="hidden" class="form-control" placeholder="Jumlah" onkeypress="return isNumberKey(event, this.id, this.value)" <?php echo 'value="'.$Jumlah.'"'; ?> />
							<!--<br />-->
							
							Satuan:<br />
							<select class="form-control" name="ddlSatuan" id="ddlSatuan" required>
								<option value="" selected>-- Pilih Satuan --</option>
								<option value="1">Pcs</option>
								<option value="2">Pax</option>
								
							</select>
							<br />
							
							<input type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Master/Alat/Insert.php');" />
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
