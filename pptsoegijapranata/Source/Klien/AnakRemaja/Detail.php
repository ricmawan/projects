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
		$NamaAyah="";
		$NamaIbu="";
		$Alamat = "";
		$Telepon = "";
		$Keterangan = "";
		$jenisKlien =2;
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
							NamaAyah,
							NamaIbu,
							Alamat,
							Telepon,
							Fax,
							Handphone,
							ContactPerson,
							NamaBank,
							NoRekening,
							Keterangan
						FROM
							master_klien
						WHERE
							klienID = $Id
						AND
							jenisKlien = 2";
							
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
				$NamaAyah=$row[6];
				$NamaIbu=$row[7];
				$Alamat = $row[8];
				$Telepon = $row[9];
				$Keterangan = $row[15];
			
				
			
				
				
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
				<h2>Master Data Klien Anak&Remaja</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Data Klien Anak&Remaja</strong>  
					</div>
					<div class="panel-body">
						<form class="col-md-6" id="PostForm" method="POST" action="" >
							Nama:<br />
							<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
							<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
							<input id="txtNama" name="txtNama" type="text" class="form-control" placeholder="Nama " required <?php echo 'value="'.$Nama.'"'; ?> />
							<input id="hdnJenis" name="hdnJenis" type="hidden"  <?php echo 'value="'.$jenisKlien.'"'; ?> />
							<input id="hdnJKelamin" name="hdnJKelamin" type="hidden" <?php echo 'value="'.$JKelamin.'"'; ?> />
							<input id="hdnTglLahir" name="hdnTglLahir" type="hidden" <?php echo 'value="'.$TglLahir.'"'; ?> />
							<br />
							Jenis Kelamin:<br />
							<select class="form-control" name="ddlJKelamin" id="ddlJKelamin" required>
								<option value="laki-laki" selected>Laki-laki</option>
								<option value="perempuan">Perempuan</option>
								
							</select>
							<br />
							
							Tempat Lahir:<br />
							<input id="txtTLahir" name="txtTLahir" type="text" class="form-control" placeholder="Tempat Lahir" required <?php echo 'value="'.$TLahir.'"'; ?> />
							<br />
							
							Tanggal Lahir:<br />
							<input id="dateTglLahir" name="dateTglLahir"  class="DatePickerMonthYearUntilNow form-control" placeholder="Tangga Lahir" required <?php echo 'value="'.$TglLahir.'"'; ?> />
							<br />
							
							Nama Ayah:<br />
							<input id="txtAyah" name="txtAyah" type="text" class="form-control" placeholder="Nama Ayah" required <?php echo 'value="'.$NamaAyah.'"'; ?> />
							<br />
							
							Nama Ibu:<br />
							<input id="txtIbu" name="txtIbu" type="text" class="form-control" placeholder="Nama Ibu" required <?php echo 'value="'.$NamaIbu.'"'; ?> />
							<br />
							
							Alamat:<br />
							<input id="txtAlamat" name="txtAlamat" type="text" class="form-control" placeholder="Alamat" required <?php echo 'value="'.$Alamat.'"'; ?> />
							<br />
							
							Telepon:<br />
							<input id="txtTelepon" name="txtTelepon" type="text" class="form-control" placeholder="Telepon" onkeypress="return isNumberKey(event, this.id, this.value)" required <?php echo 'value="'.$Telepon.'"'; ?> />
							<br />
							
							Keterangan:<br />
							<input id="txtKeterangan" name="txtKeterangan" type="text" class="form-control" placeholder="Keterangan"  required <?php echo 'value="'.$Keterangan.'"'; ?> />
							<br />
							
							<input type="button" class="btn btn-default" value="Simpan" onclick="SubmitForm('./Klien/AnakRemaja/Insert.php');" />
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
		
			$(document).ready(function () {
				$("#ddlJKelamin").val($("#hdnJKelamin").val());
				$("#dateTglLahir").val($("#hdnTglLahir").val());
			});
		
		</script>
	</body>
</html>
