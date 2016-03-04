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
		$JKelamin="";
		$TLahir="";
		$TglLahir="";
		$Orangtua="";
		$Alamat = "";
		$Telepon = "";
		$StatusMarital="";
		$Pekerjaan="";
		$Keterangan = "";
		$jenisKlien =4;
		$IsEdit = 0;
		
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {
			if($Id !=0) {
				$IsEdit = 1;
				//$Content = "Place the content here";
				$sql = "SELECT
							KlienID,
							JenisKlien,
							Nama,
							JenisKelamin,
							TempatLahir,
							DATE_FORMAT(TanggalLahir, '%d-%m-%Y'),
							OrangTua,
							Alamat,
							Telepon,
							Fax,
							Handphone,
							ContactPerson,
							NamaBank,
							NoRekening,
							StatusMarital,
							Pekerjaan,
							Keterangan
						FROM
							master_klien
						WHERE
							klienID = $Id
						AND
							jenisKlien = 4";
							
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}				
				$row=mysql_fetch_row($result);
				$Id = $row[0];
				$jenisKlien = $row[1];
				$Nama = $row[2];
				$JKelamin=$row[3];
				$TLahir=$row[4];
				$TglLahir=$row[5];
				$Orangtua=$row[6];
				$Alamat = $row[7];
				$Telepon = $row[8];
				$StatusMarital = $row[14];
				$Pekerjaan = $row[15];
				$Keterangan = $row[16];
				
				
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
				<h2>Master Data Klien Dewasa</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Data Klien Dewasa</strong>  
					</div>
					<div class="panel-body">
						<form class="col-md-6" id="PostForm" method="POST" action="" >
							Nama:<br />
							<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="txtNama" name="txtNama" type="text" class="form-control" placeholder="Nama " required <?php echo 'value="'.$Nama.'"'; ?> />
							<input id="hdnJenis" name="hdnJenis" type="hidden"  <?php echo 'value="'.$jenisKlien.'"'; ?> />
							<input id="hdnJKelamin" name="hdnJKelamin" type="hidden" <?php echo 'value="'.$JKelamin.'"'; ?> />
							<input id="hdnMarital" name="hdnMarital" type="hidden" <?php echo 'value="'.$StatusMarital.'"'; ?> />
							<input id="hdnTglLahir" name="hdnTglLahir" type="hidden" <?php echo 'value="'.$TglLahir.'"'; ?> />
							<br />
							Jenis Kelamin:<br />
							<select class="form-control" name="ddlJKelamin" id="ddlJKelamin" required>
							
								<option value="" selected>--Pilih Jenis Kelamin--</option>
								<option value="laki-laki">Laki-laki</option>
								<option value="perempuan">Perempuan</option>
								
							</select>
							<br />
							
							Tempat Lahir:<br />
							<input id="txtTLahir" name="txtTLahir" type="text" class="form-control" placeholder="Tempat Lahir" required <?php echo 'value="'.$TLahir.'"'; ?> />
							<br />
							
							Tanggal Lahir:<br />
							<input id="dateTglLahir" name="dateTglLahir"  class="DatePickerMonthYearUntilNow form-control" placeholder="Tanggal Lahir" required <?php echo 'value="'.$TglLahir.'"'; ?> />
							<br />
							
							Orangtua:<br />
							<input id="txtOrtu" name="txtOrtu" type="text" class="form-control" placeholder="Orangtua" required <?php echo 'value="'.$Orangtua.'"'; ?> />
							<br />
							
							Alamat:<br />
							<input id="txtAlamat" name="txtAlamat" type="text" class="form-control" placeholder="Alamat" required <?php echo 'value="'.$Alamat.'"'; ?> />
							<br />
							
							Telepon:<br />
							<input id="txtTelepon" name="txtTelepon" type="text" class="form-control" placeholder="Telepon" onkeypress="return isNumberKey(event, this.id, this.value)" required <?php echo 'value="'.$Telepon.'"'; ?> />
							<br />
							
							Status Marital:<br />
							<select class="form-control" name="ddlMarital" id="ddlMarital" required>
							
								<option value="" selected>--Pilih Status Marital--</option>
								<option value="lajang">Lajang</option>
								<option value="menikah">Menikah</option>
								<option value="bercerai">Bercerai</option>
								
							</select>
							<br />
							
							Pekerjaan:<br />
							<input id="txtPekerjaan" name="txtPekerjaan" type="text" class="form-control" placeholder="Pekerjaan"  required <?php echo 'value="'.$Pekerjaan.'"'; ?> />
							<br />
							
							Keterangan:<br />
							<input id="txtKeterangan" name="txtKeterangan" type="text" class="form-control" placeholder="Keterangan"  required <?php echo 'value="'.$Keterangan.'"'; ?> />
							<br />
							
							<input type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Klien/Dewasa/Insert.php');" />
						</form>
					</div>
				</div>
			</div>
		</div>
			<script>
		
			$(document).ready(function () {
				$("#ddlJKelamin").val($("#hdnJKelamin").val());
				$("#ddlMarital").val($("#hdnMarital").val());
				$("#dateTglLahir").val($("#hdnTglLahir").val());
			});
		
		</script>
	</body>
</html>
