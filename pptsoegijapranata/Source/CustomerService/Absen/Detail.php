<?php
	if(isset($_GET['id'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$Id = $_GET['id'];
		$Tanggal = "";
		$Jenis = "";
		$KlienID = "";
		$ContactPerson = "";
		$MaksKlien = "";
		$Keluhan = "";
		$Prognosis = "";
		$Keterangan = "";
		$Report = "";
		$Nama = "";
		$Alamat = "";
		$Telepon = "";
		$Data = "";
		$UangMuka = "0.00";
		$Pembayaran = "0.00";
		$Pajak = "0";
		$Diskon = "0";
		$TanggalLahir = "";
		$Usia = "";
		$SupervisorID = "";
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {
			if($Id !=0) {
				//$Content = "Place the content here";
				$sql = "SELECT
							CS.TransaksiID,
							DATE_FORMAT(CS.Tanggal, '%d-%m-%Y'),
							CS.Jenis,
							CS.KlienID,
							K.ContactPerson,
							CS.MaksimalKlien,
							CS.Keluhan,
							CS.Prognosis,
							CS.Keterangan,
							CS.Report,
							K.Nama,
							K.Alamat,
							K.Telepon,
							CS.UangMuka,
							CS.Pembayaran,
							CS.Pajak,
							CS.Diskon,
							DATE_FORMAT(K.TanggalLahir, '%d-%m-%Y'),
							CONCAT(TIMESTAMPDIFF( YEAR, TanggalLahir, now() ) , ' Tahun ', TIMESTAMPDIFF( MONTH, TanggalLahir, now() ) % 12, ' Bulan ', FLOOR( TIMESTAMPDIFF( DAY, TanggalLahir, now() ) % 30.4375 ), ' Hari ') AS Usia,
							CS.KeteranganPembayaran,
							CS.SupervisorID,
							CS.Denda
						FROM
							transaksi_customerservice CS
							JOIN master_klien K
								ON K.KlienID = CS.KlienID
						WHERE
							CS.TransaksiID = $Id";
							
				if (! $result=mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}
				$row=mysql_fetch_row($result);
				$Id = $row[0];
				$Tanggal = $row[1];
				$Jenis = $row[2];
				$KlienID = $row[3];
				$ContactPerson = $row[4];
				$MaksKlien = $row[5];
				$Keluhan = $row[6];
				$Prognosis = $row[7];
				$Keterangan = $row[8];
				$Report = $row[9];
				$Nama = $row[10];
				$Alamat = $row[11];
				$Telepon = $row[12];
				$UangMuka = $row[13];
				$Pembayaran = $row[14];
				$Pajak = $row[15];
				$Diskon = $row[16];
				$TanggalLahir = $row[17];
				$Usia = $row[18];
				$KeteranganPembayaran = $row[19];
				$SupervisorID = $row[20];
				$Denda = $row[21];
				$sql = "SELECT 
						DetailID,
						KonsultanID,
						AsistenID,
						TerapisID,
						LayananID,
						Nama,
						Pendidikan,
						NoPsikogram,
						Jumlah,
						Harga,
						StatusProcess,
						FeeKonsultan,
						Harga_Layanan
					FROM
						transaksi_rincicustomerservice
					WHERE
						TransaksiID = '$Id'";
				if(!$result = mysql_query($sql, $dbh)) {
					echo mysql_error();
					return 0;
				}
				$rowcount = mysql_num_rows($result);
				if($rowcount > 0) {
					//$DetailID = array();
					$Data = array();
					while($row = mysql_fetch_row($result)) {
						//array_push($DetailID, $row[0]);
						array_push($Data, "'".$row[0]."', '".$row[1]."', '".$row[2]."', '".$row[3]."', '".$row[4]."', '".$row[5]."', '".$row[6]."', '".$row[7]."', '".$row[8]."', '".$row[9]."', '".$row[10]."', '".$row[11]."', '".$row[12]."'");
					}
					//$DetailID = implode(",", $DetailID);
					$Data = implode("|", $Data);
				}
				else {
					//$DetailID = "";
					$Data = "";
				}
			}
		}
	}
?>
<html>
	<head>
		<style>
			.custom-combobox {
				position: relative;
				display: inline-block;
				width: 100%;
			}
			.custom-combobox-input {
				margin: 0;
				padding: 5px 10px;
				display: block;
				width: 150px;
				height: 34px;
				padding: 6px 12px;
				font-size: 14px;
				line-height: 1.42857143;
				color: #555;
				background-color: #fff;
				background-image: none;
				border: 1px solid #ccc;
				border-radius: 4px;
				-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
				box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
				-webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
				-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
				transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
			}
			.btn-group {
				width: 100%;
				display: block;
			}
			.col-md-2 {
				display: -webkit-box;
				-webkit-box-align: center;
				height: 34px;
			}
			.col-md-3 {
				display: -webkit-box;
				-webkit-box-align: center;
				height: 34px;
			}
			.col-md-1 {
				display: -webkit-box;
				-webkit-box-align: center;
				height: 34px;
				width: 1%;
			}
			.col-md-6 {
				display: -webkit-box;
				-webkit-box-align: center;
			}
			.ui-autocomplete {
				font-family: Open Sans, sans-serif; 
				font-size: 14px;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<h2>Absen Peserta</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-2">
					No Kwitansi:
				</div>
				<div class="col-md-3">
					<?php echo $Id; ?>
				</div>
				<div class="col-md-2">
					&nbsp;
				</div>
				<div class="col-md-2">
					<?php if($Jenis == 2 || $Jenis == 4) echo "Usia:"; ?>
				</div>
				<div class="col-md-3">
					<?php if($Jenis == 2 || $Jenis == 4) echo $Usia; ?>
				</div>
			</div>
		</div>
		<br />
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-2">
					Tanggal :
				</div>
				<div class="col-md-3">
					<?php echo $Tanggal ?>
				</div>
				<div class="col-md-2">
					&nbsp;
				</div>
				<div class="col-md-2">
					Alamat:
				</div>
				<div class="col-md-3">
					<?php echo $Alamat; ?>
				</div>
			</div>
		</div>
		<br />
		<div class="row">
			<div class="col-md-12">
				<div class="col-md-2">
					Nama Klien:
				</div>
				<div class="col-md-3">
					<?php echo $Nama; ?>
				</div>
				<div class="col-md-2">
					&nbsp;
				</div>
				<div class="col-md-2">
					Telepon:
				</div>
				<div class="col-md-3">
					<?php echo $Telepon ?>
				</div>
			</div>
		</div>
		<hr />
		<br />
		<div class="row">	
			<div class="col-md-12">
				<!--    Bordered Table  -->
				<div class="panel panel-default">
					<div class="panel-heading">
						Detail Transaksi
					</div>
					<!-- /.panel-heading -->
					<div class="panel-body">
						<form id="PostForm" method="POST" action="" >
							<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
							<input id="hdnData" name="hdnData" type="hidden" <?php echo 'value="'.$Data.'"'; ?> />
							<input id="hdnMaksKlien" name="hdnMaksKlien" type="hidden" <?php echo 'value="'.$MaksKlien.'"'; ?> />
							<input id="hdnJenis" name="hdnJenis" type="hidden" <?php echo 'value="'.$Jenis.'"'; ?> />
							<input id="hdnRow" name="hdnRow" type="hidden" <?php echo 'value="'.$rowcount.'"'; ?> />
							<input id="hdnKlien" name="hdnKlien" type="hidden" <?php echo 'value="'.$Nama.'"'; ?> />
							<input id="hdnSupervisor" name="hdnSupervisor" type="hidden" <?php echo 'value="'.$SupervisorID.'"'; ?> />
							<input id="hdnTanggal" name="hdnTanggal" type="hidden" <?php echo 'value="'.$Tanggal.'"'; ?> />
							<input id="hdnStatusProcess" name="hdnStatusProcess" type="hidden" <?php echo 'value="'.$STATUS_PROCESS.'"'; ?> />
							<div class="row">
								<div class="col-md-6">
									<select class="form-control" name="ddlSupervisor" id="ddlSupervisor" multiple="multiple" >
										<?php
											$sql = "SELECT * FROM master_menu";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_row($result)) {
												echo "<option value='".$row[0]."'>".$row[1]."</option>";
											}
										?>
									</select>
								</div>
							</div>
							<br />
							<?php if($Jenis == 1 || $Jenis == 3) { ?>
								<div class="table-responsive table-bordered">
									<table class="table" id="datainput">
										<thead>
											<td style="text-align:center;">No</td>
											<td style="text-align:center;">Nama Karyawan</td>
											<td style="text-align:center;">No Psikogram</td>
											<td style="text-align:center;">Pendidikan</td>
											<td style="text-align:center;">Status Proses</td>
											<td style="text-align:center;">Konsultan</td>
											<td style="text-align:center;">Asisten</td>
											<td style="text-align:center;">Layanan</td>
											<td style="text-align:center;">Jumlah <br />(Jam)</td>
											<td style="text-align:center;">Harga</td>
										</thead>
										<tbody>
											<tr id='' align='center' style='display:none;'>
												<td id='nota' name='nota'></td>
												<td>
													<input type="text" id="txtNama" name="txtNama" style="width:125px;" class="form-control" />
													<input type="hidden" id="hdnDetailID" name="hdnDetailID" value="0" />
													<input type="hidden" id="hdnFeeKonsultan" name="hdnFeeKonsultan" value="0" />
													<input type="hidden" id="hdnHargaLayanan" name="hdnHargaLayanan" value="0" />
												</td>
												<td><input type="text" id="txtPsikogram" name="txtPsikogram" style="width:85px;" class="form-control" /></td>
												<td>
													<select id="ddlPendidikan" name="ddlPendidikan" style="width:85px;" class="form-control" >
														<option value=0>-Pilih-</option>
														<option value=1>SD</option>
														<option value=2>SMP</option>
														<option value=3>SMA</option>
														<option value=4>S1</option>
														<option value=5>S2</option>
														<option value=6>S3</option>
													</select>
												</td>
												<td>
													<select id="ddlProses" name="ddlProses" row=1 onchange="updateHarga(this.getAttribute('row'), this.value)" class="form-control proses" >
														<option value=1>Biasa</option>
														<option value=2>Kilat</option>
													</select>
												</td>
												<td>
													<select class="form-control" name="ddlKonsultan" id="ddlKonsultan" style="width:85px;" >
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_konsultan";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."'>".$row[1]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<select class="form-control" name="ddlAsisten" id="ddlAsisten" style="width:85px;" >
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_asisten";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."'>".$row[1]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<select id="ddlLayanan" name="ddlLayanan" style="width:85px;" onchange="BindSatuan(this.getAttribute('row'))" class="form-control" >
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_layanan";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."' harga=".$row[3]." satuan='".$row[5]."' fee='".$row[4]."' >".$row[2]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<input value=1 type="text" id="txtJumlah" name="txtJumlah" onkeypress="return isNumberKey(event)" style="width:50px; text-align: center;" onchange="BindHarga(this.getAttribute('row'))" class="form-control" readonly />
												</td>
												<td><input type="text" id="txtHarga" name="txtHarga" class="form-control txtHarga" value="0.00" onchange="calculate()" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" style="width:125px;text-align:right;" /></td>
											</tr>
											<tr id='num1' align='center'>
												<td id='nota1' name='nota1'>1</td>
												<td>
													<input type="text" id="txtNama1" name="txtNama1" class="form-control" row=1 style="width:125px;" required />
													<input type="hidden" id="hdnDetailID1" name="hdnDetailID1" value="0" />
													<input type="hidden" id="hdnFeeKonsultan1" name="hdnFeeKonsultan1" value="0" />
													<input type="hidden" id="hdnHargaLayanan1" name="hdnHargaLayanan1" value="0" />
												</td>
												<td><input type="text" id="txtPsikogram1" name="txtPsikogram1" style="width:85px;" row=1 class="form-control" required /></td>
												<td>
													<select id="ddlPendidikan1" name="ddlPendidikan1" class="form-control" row=1 style="width:85px;" required >
														<option value=0>-Pilih-</option>
														<option value=1>SD</option>
														<option value=2>SMP</option>
														<option value=3>SMA</option>
														<option value=4>S1</option>
														<option value=5>S2</option>
														<option value=6>S3</option>
													</select>
												</td>
												<td>
													<select id="ddlProses1" name="ddlProses1" row=1 class="form-control" onchange="updateHarga(this.getAttribute('row'), this.value)" required >
														<option value=1>Biasa</option>
														<option value=2>Kilat</option>
													</select>
												</td>
												<td>
													<select class="form-control" name="ddlKonsultan1" id="ddlKonsultan1" style="width:85px;" required>
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_konsultan";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."'>".$row[1]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<select class="form-control" name="ddlAsisten1" id="ddlAsisten1" style="width:85px;" >
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_asisten";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."'>".$row[1]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<select id="ddlLayanan1" name="ddlLayanan1" onchange="BindSatuan(this.getAttribute('row'))" class="form-control layanan" row=1 style="width:85px;" required >
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_layanan";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."' harga=".$row[3]." satuan='".$row[5]."' fee='".$row[4]."' >".$row[2]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<input type="text" value=1 id="txtJumlah1" name="txtJumlah1" style="width:50px; text-align: center;" onkeypress="return isNumberKey(event)" onchange="BindHarga(this.getAttribute('row'))" row=1 class="form-control" readonly required/>
												</td>
												<td><input type="text" id="txtHarga1" row=1 name="txtHarga1" class="form-control txtHarga" onchange="calculate()" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" style="width:125px;text-align:right;" value="0.00" required /></td>
											</tr>
										</tbody>
									</table>
								</div>
							<?php } else { ?>
								<div class="table-responsive table-bordered">
									<table class="table" id="datainput">
										<thead>
											<td>No</td>
											<td>Konsultan</td>
											<td>Terapis</td>
											<td>Asisten</td>
											<td>Layanan</td>
											<td>Jumlah <br />(Jam)</td>
											<td>Harga</td>
										</thead>
										<tbody>
											<tr id='' style='display:none;'>
												<td id='nota' name='nota'></td>
												<td>
													<select class="form-control" name="ddlKonsultan" id="ddlKonsultan" style="width:100px;" >
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_konsultan";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."'>".$row[1]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<select class="form-control" name="ddlTerapis" id="ddlTerapis" style="width:100px;" >
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_terapis";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."'>".$row[1]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<select class="form-control" name="ddlAsisten" id="ddlAsisten" style="width:100px;" >
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_asisten";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."'>".$row[1]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<input type="hidden" id="hdnDetailID" name="hdnDetailID" value="0" />
													<input type="hidden" id="hdnFeeKonsultan" name="hdnFeeKonsultan" value="0" />
													<input type="hidden" id="hdnHargaLayanan" name="hdnHargaLayanan" value="0" />
													<select id="ddlLayanan" name="ddlLayanan" style="width:100px;" onchange="BindSatuan(this.getAttribute('row'))" class="form-control layanan" row=1 >
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_layanan";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."' harga=".$row[3]." satuan='".$row[5]."' fee='".$row[4]."' >".$row[2]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<input type="text" id="txtJumlah" name="txtJumlah" style="width:50px; text-align:center;" onkeypress="return isNumberKey(event)" value=1 onchange="BindHarga(this.getAttribute('row'))" row=1 readonly class="form-control" />
												</td>
												<td>
													<input type="text" id="txtHarga" row=1 name="txtHarga" class="form-control txtHarga" onchange="calculate()" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" style="width:200px; text-align:right;" value="0.00" />
												</td>
											</tr>
											<tr>
												<td id='nota' name='nota'>1</td>
												<td>
													<select class="form-control" name="ddlKonsultan1" id="ddlKonsultan1" style="width:100px;" required>
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_konsultan";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."'>".$row[1]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<select class="form-control" name="ddlTerapis1" id="ddlTerapis1" style="width:100px;" >
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_terapis";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."'>".$row[1]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<select class="form-control" name="ddlAsisten1" id="ddlAsisten1" style="width:100px;" required>
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_asisten";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."'>".$row[1]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<input type="hidden" id="hdnDetailID1" name="hdnDetailID1" value="0" />
													<input type="hidden" id="hdnFeeKonsultan1" name="hdnFeeKonsultan1" value="0" />
													<input type="hidden" id="hdnHargaLayanan1" name="hdnHargaLayanan1" value="0" />
													<select id="ddlLayanan1" name="ddlLayanan1" style="width:100px;" onchange="BindSatuan(this.getAttribute('row'))" class="form-control layanan" row=1 required >
														<option value=0>-Pilih-</option>
														<?php
															$sql = "SELECT * FROM master_layanan";
															if(!$result = mysql_query($sql, $dbh)) {
																echo mysql_error();
																return 0;
															}
															while($row = mysql_fetch_row($result)) {
																echo "<option value='".$row[0]."' harga=".$row[3]." satuan='".$row[5]."' fee='".$row[4]."' >".$row[2]."</option>";
															}
														?>
													</select>
												</td>
												<td>
													<input type="text" id="txtJumlah1" name="txtJumlah1" style="width:50px; text-align:center;" onkeypress="return isNumberKey(event)" value=1 onchange="BindHarga(this.getAttribute('row'))" row=1 readonly required class="form-control" />
												</td>
												<td>
													<input type="text" id="txtHarga1" row=1 name="txtHarga1" class="form-control txtHarga" onchange="calculate()" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" style="width:200px; text-align:right;" value="0.00" required />
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<br />
							<?php } ?>
							<br />
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-2">
										Keterangan :
									</div>
									<div class="col-md-3">
										<select id="ddlKeterangan" name="ddlKeterangan" class="form-control" >
											<option value=1>Cash</option>
											<option value=2>Transfer</option>
										</select>
									</div>
									<div class="col-md-1"></div>
									<div class="col-md-2">
										Grand Total :
									</div>
									<div class="col-md-3">
										<input type="text" name="txtTotal" style="text-align:right;" id="txtTotal" class="form-control col-md-3" value="0.00" readonly />
									</div>
									<div class="col-md-1"></div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-2">
										Diskon (%) :
									</div>
									<div class="col-md-3">
										<input type="text" <?php echo 'value="'.$Diskon.'"'; ?> name="txtDiskon" style="text-align:right;" onchange="calculate()" onkeypress="return PercentNumber(event, this.id, this.value)" id="txtDiskon" class="form-control col-md-3" />
									</div>
									<div class="col-md-1" style="text-align:left;"></div>
									<div class="col-md-2">
										Uang Muka :
									</div>
									<div class="col-md-3">
										<input type="text" <?php echo 'value="'.number_format($UangMuka,2,".",",").'"'; ?>  name="txtUangMuka" style="text-align:right;" id="txtUangMuka" onchange="calculatePayment(this.id)" onkeypress="return isNumberKey(event, this.id)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" class="form-control col-md-3" value="0,00" />
									</div>
									<div class="col-md-1"></div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-12">
									<div class="col-md-2">
										Pajak (%) :
									</div>
									<div class="col-md-3">
										<input type="text" <?php echo 'value="'.$Pajak.'"'; ?> name="txtPajak" style="text-align:right;" onchange="calculate()" onkeypress="return PercentNumber(event, this.id, this.value)" id="txtPajak" class="form-control col-md-3" />
									</div>
									<div class="col-md-1"></div>
									<div class="col-md-2">
										Pembayaran :
									</div>
									<div class="col-md-3">
										<input type="text" <?php echo 'value="'.number_format($Pembayaran,2,".",",").'"'; ?> name="txtPembayaran" style="text-align:right;" id="txtPembayaran" onchange="calculatePayment(this.id)" onkeypress="return isNumberKey(event, this.id)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" class="form-control col-md-3" />
									</div>
									<div class="col-md-1"></div>
								</div>
							</div>
							<br />

							<div class="row">
								<div class="col-md-12">
									<div class="col-md-2" style="color:red;">
										Denda :
									</div>
									<div class="col-md-3">
										<input type="text" name="txtDenda" style="text-align:right;" onchange="calculate()" id="txtDenda" class="form-control col-md-3" <?php echo 'value="'.number_format($Denda,2,".",",").'"'; ?> onkeypress="return isNumberKey(event, this.id)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)"/>
									</div>
									<div class="col-md-1"></div>
									<div class="col-md-2">
										Kekurangan :
									</div>
									<div class="col-md-3">
										<input type="text" name="txtKekurangan" style="text-align:right;" id="txtKekurangan" class="form-control col-md-3" value="0.00" readonly />
									</div>
									<div class="col-md-1"></div>
								</div>
							</div>
							<br />
							<br />
							<input type="hidden" id="record" name="record" value=1 />
							<input type="hidden" id="recordnew" name="recordnew" value=1 />
						</form>
						<div class="row">
							<div class="col-md-12">
								<div class="col-md-6">
									<button class="btn btn-primary" id="btnAdd"><i class="fa fa-plus "></i> Tambah</button>&nbsp;&nbsp;
									<button class="btn btn-danger" id="btnDelete"><i class="fa fa-close"></i> Hapus</button>
								</div>
								<div class="col-md-1">
								</div>
								<div class="col-md-5" style="float:right;">
									<button class="btn btn-default" id="btnPrintAbsen" style="color:red;" onclick="PrintAbsen()" ><i class="fa fa-print" style="color: black;"></i> Print Absen</button>&nbsp;&nbsp;
									<button class="btn btn-default" id="btnSave"  onclick="SubmitValidate();" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
									<button class="btn btn-default" id="btnPrint" onclick="PrintInvoice()" ><i class="fa fa-print"></i> Print</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function () {
				if($("#hdnJenis").val() == 2 || $("#hdnJenis").val() == 4) {
					$("#btnPrintAbsen").attr("disabled", "disabled");
				}
				$("#ddlSupervisor").multiselect({
					includeSelectAllOption: true,
					selectAllText: 'Pilih Semua',
					nonSelectedText: 'Tidak Ada Supervisor yang Dipilih',
					nSelectedText: 'Supervisor dipilih',
					allSelectedText: 'Semua Supervisor Dipilih',
					numberDisplayed: 10
				});
				$(".multiselect").hide();

				var SupervisorConfig = {
					includeSelectAllOption: true,
					selectAllText: 'Pilih Semua',
					nonSelectedText: 'Tidak Ada Supervisor yang Dipilih',
					nSelectedText: 'Supervisor dipilih',
					allSelectedText: 'Semua Supervisor Dipilih',
					numberDisplayed: 10
				};
				$("#ddlSupervisor").multiselect("select", $("#hdnSupervisor").val().split(","));
				$("#ddlSupervisor").multiselect("setOptions", SupervisorConfig);
				$("#ddlSupervisor").multiselect("rebuild");

				$("#btnAdd").on("click", function() {
					var jenis = $("#hdnJenis").val();
					var count = $("#datainput tbody tr").length - 1;
					count++;
					var $clone = $("#datainput tbody tr:first").clone();
					$clone.find("#nota").text(count);
					$clone.find("#nota").attr("id", "nota" + count);
					$clone.find("#nota").attr("name", "nota" + count);
					$clone.removeAttr("style");
					$clone.attr({
						id: "num" + count,
						name: "num" + count

					});
					$clone.find("input, select").each(function(){
						//var temp = $(this).attr("id") + (count - 1);
						$(this).attr({
							id: $(this).attr("id") + count,
							name: $(this).attr("name") + count,
							row: count,
							required: ""
						});				
						//$(this).val($("#" + temp).val());
					});
					$("#datainput tbody").append($clone);
					if($("#hdnJenis").val() == 1 || $("#hdnJenis").val() == 3) $("#ddlAsisten" + count).removeAttr("required");
					if($("#hdnJenis").val() == 2 || $("#hdnJenis").val() == 4) $("#ddlTerapis" + count).removeAttr("required");
					$("#recordnew").val(count);
			
					if(($("#hdnJenis").val() == "1" || $("#hdnJenis").val() == "3") && count >= parseInt($("#hdnMaksKlien").val())) { 
						$(this).attr("disabled", true);
					}
					if(($("#hdnJenis").val() == "1" || $("#hdnJenis").val() == "3") && count >= 50) { 
						$(".multiselect").show();
					}
				});
				$("#btnDelete").on("click", function() {
					var count = $("#datainput tbody tr").length - 1;
					if(count > 1) {
						$('#datainput tr:last').remove();
						$("#recordnew").val(count-1);
						$("#btnAdd").attr("disabled", false)
					}
					if(($("#hdnJenis").val() == "1" || $("#hdnJenis").val() == "3") && (count-1) < 50) { 
						$(".multiselect").hide();
						$('#ddlSupervisor').multiselect('deselectAll', false);
						$('#ddlSupervisor').multiselect('updateButtonText');
					}
					calculate();
				});
				if(parseInt($("#hdnRow").val()) > 0) {
					//alert("Test");
					var data = $("#hdnData").val();
					var item = data.split("|");
					var row = item.length;
					var count = 0;
					$('#datainput tbody:last > tr:not(:first)').remove();
					if($("#hdnJenis").val() == "1" || $("#hdnJenis").val() == "3") {
						for(var i=0; i<row; i++) {
							$("#btnAdd").click();
							count++;
							//set values
							var d = item[i].split("', '");
							$("#nota").text(count);
							$("#hdnDetailID" + count).val(d[0].replace("'", ""));
							$("#txtNama" + count).val(d[5].replace("'", ""));
							$("#txtPsikogram" + count).val(d[7].replace("'", ""));
							$("#ddlPendidikan" + count).val(d[6].replace("'", ""));
							$("#ddlKonsultan" + count).val(d[1].replace("'", ""));
							$("#ddlAsisten" + count).val(d[2].replace("'", ""));
							$("#ddlLayanan" + count).val(d[4].replace("'", ""));
							$("#txtJumlah" + count).val(d[8].replace("'", ""));
							$("#txtHarga" + count).val(returnRupiah(d[9].replace("'", "")));
							$("#ddlProses" + count).val(d[10].replace("'", ""));
							$("#hdnFeeKonsultan" + count).val(d[11].replace("'", ""));
							$("#hdnHargaLayanan" + count).val(d[12].replace("'", ""));
							if($("#ddlLayanan" + count + " option:selected").attr("satuan") == "2") $("#txtJumlah" + count).removeAttr("readonly");
							$("#record").val(count);
							$("#recordnew").val(count);
						}
					}
					else {
						for(var i=0; i<row; i++) {
							$("#btnAdd").click();
							count++;
							//set values
							var d = item[i].split("', '");
							$("#nota").text(count);
							$("#hdnDetailID" + count).val(d[0].replace("'", ""));
							$("#ddlKonsultan" + count).val(d[1].replace("'", ""));
							$("#ddlTerapis" + count).val(d[3].replace("'", ""));
							$("#ddlAsisten" + count).val(d[2].replace("'", ""));
							$("#ddlLayanan" + count).val(d[4].replace("'", ""));
							$("#txtJumlah" + count).val(d[8].replace("'", ""));
							$("#txtHarga" + count).val(returnRupiah(d[9].replace("'", "")));
							$("#hdnFeeKonsultan" + count).val(d[11].replace("'", ""));
							$("#hdnHargaLayanan" + count).val(d[12].replace("'", ""));
							if($("#ddlLayanan" + count + " option:selected").attr("satuan") == "2") $("#txtJumlah" + count).removeAttr("readonly");
							$("#record").val(count);
							$("#recordnew").val(count);
						}
							
					}
					calculate();
				}

			});
			function BindSatuan(row) {
				var jumlah;
				var process = $("#ddlProses" + row).val();
				var harga = 0;
				var hargaLayanan;
				var FeeKonsultan = 0;
				if($("#ddlLayanan" + row).val() == 0) {
					$("#txtJumlah" + row).attr("readonly", "readonly");
					$("#txtJumlah" + row).val(1);
					//harga = 0;
					hargaLayanan = 0;
					jumlah = 1;
					FeeKonsultan = 0;
				}
				else {
					if($("#ddlLayanan" + row + " option:selected").attr("satuan") == "2") $("#txtJumlah" + row).removeAttr("readonly");
					else {
						$("#txtJumlah" + row).attr("readonly", "readonly");
						$("#txtJumlah" + row).val(1);
					}
					hargaLayanan = parseFloat($("#ddlLayanan" + row + " option:selected").attr("harga"));
					jumlah = $("#txtJumlah" + row).val();
					FeeKonsultan = parseFloat($("#ddlLayanan" + row + " option:selected").attr("fee"));
				}
				$("#hdnFeeKonsultan" + row).val(FeeKonsultan);
				$("#hdnHargaLayanan" + row).val(hargaLayanan);
				var total = hargaLayanan * jumlah;
				if(process == 2) total += parseFloat($("#hdnStatusProcess").val());
				$("#txtHarga" + row).val(returnRupiah((total).toString()));
				calculate();
			}
			function BindHarga(row) {
				if($("#txtJumlah" + row).val() == "" || $("#txtJumlah" + row).val() == 0) $("#txtJumlah" + row).val(1);
				var harga = 0;
				var process = $("#ddlProses" + row).val();
				var hargaLayanan = $("#ddlLayanan" + row + " option:selected").attr("harga");
				var jumlah = $("#txtJumlah" + row).val();
				var total = hargaLayanan * jumlah;
				if(process == 2) total += parseFloat($("#hdnStatusProcess").val());

				$("#txtHarga" + row).val(returnRupiah((total).toString()));
				calculate();
			}
			function updateHarga(baris, value) {
				var harga = 0;
				harga = parseFloat($("#txtHarga" + baris).val().replace(/\,/g, ""));
				var StatusProcess = parseFloat($("#hdnStatusProcess").val());
				if(value == 2) harga += StatusProcess;
				else harga -= StatusProcess;
				$("#txtHarga" + baris).val(returnRupiah(harga.toString()));
				calculate();
			}
			function calculate() {
				var total = 0;
				var UangMuka = $("#txtUangMuka").val().replace(/\,/g, "");
				var Pembayaran = $("#txtPembayaran").val().replace(/\,/g, "");
				var Denda = parseFloat($("#txtDenda").val().replace(/\,/g, ""));
				if($("#txtDiskon").val() > 100) $("#txtDiskon").val(100);
				if($("#txtPajak").val() > 100) $("#txtPajak").val(100);
				if($("#txtDiskon").val() == "") $("#txtDiskon").val(0);
				if($("#txtPajak").val() == "") $("#txtPajak").val(0);
				var Diskon = $("#txtDiskon").val();
				var Pajak = $("#txtPajak").val();
				$(".txtHarga").each(function() {
					total += parseFloat($(this).val().replace(/\,/g, ""));
				});
				total += Denda;
				total -= ((total * Diskon) /100);
				total += ((total * Pajak) /100);
				$("#txtTotal").val(returnRupiah(total.toString()));
				if((parseFloat(UangMuka) + parseFloat(Pembayaran)) > parseFloat(total)) {
					$("#txtUangMuka").notify("Uang Muka & Pembayaran Melebihi Grand Total!", { position:"right", className:"warn", autoHideDelay: 2000 });
					$("#txtPembayaran").notify("Uang Muka & Pembayaran Melebihi Grand Total!", { position:"right", className:"warn", autoHideDelay: 2000 });
					$("#txtUangMuka").val(returnRupiah("0"));
					$("#txtPembayaran").val(returnRupiah("0"));
					UangMuka = 0;
					Pembayaran = 0;
				}
				$("#txtKekurangan").val(returnRupiah((total - UangMuka - Pembayaran).toString()));
			}
			function calculatePayment(ID) {
				var total = 0;
				var UangMuka = $("#txtUangMuka").val().replace(/\,/g, "");
				var Pembayaran = $("#txtPembayaran").val().replace(/\,/g, "");
				var Denda = parseFloat($("#txtDenda").val().replace(/\,/g, ""));				
				if($("#txtDiskon").val() > 100) $("#txtDiskon").val(100);
				if($("#txtPajak").val() > 100) $("#txtPajak").val(100);
				if($("#txtDiskon").val() == "") $("#txtDiskon").val(0);
				if($("#txtPajak").val() == "") $("#txtPajak").val(0);
				var Diskon = $("#txtDiskon").val();
				var Pajak = $("#txtPajak").val();
				$(".txtHarga").each(function() {
					total += parseFloat($(this).val().replace(/\,/g, ""));
				});
				total += Denda;
				total -= ((total * Diskon) /100);
				total += ((total * Pajak) /100);
				$("#txtTotal").val(returnRupiah(total.toString()));
				if((parseFloat(UangMuka) + parseFloat(Pembayaran)) > parseFloat(total)) {
					$("#" + ID).notify("Uang Muka & Pembayaran Melebihi Grand Total!", { position:"right", className:"warn", autoHideDelay: 2000 });
					$("#" + ID).val(returnRupiah("0"));
					if(ID == "txtUangMuka") UangMuka = 0;
					else Pembayaran = 0;
				}
				$("#txtKekurangan").val(returnRupiah((total - UangMuka - Pembayaran).toString()));
			}
			function SubmitValidate() {
				var PassValidate = 1;
				var FirstFocus = 0;
				$(".layanan").each(function() {
					if($(this).val() == null) {
						PassValidate = 0;
						$(this).next().notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
						if(FirstFocus == 0) $(this).next().focus();
						FirstFocus = 1;
					}
				});
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else SubmitForm("./CustomerService/Absen/Insert.php");
				//alert("test");
			}
			function PrintInvoice() {
				var PassValidate = 1;
				var FirstFocus = 0;
				$(".form-control").each(function() {
					if($(this).hasAttr('required')) {
						if($(this).val() == "" || $(this).val() == "0") {
							PassValidate = 0;
							$(this).notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $(this).focus();
							FirstFocus = 1;
						}
					}
				});
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					var ID = $("#hdnId").val();
					$("#loading").show();
					$.ajax({
						url: "./CustomerService/Absen/Print.php",
						type: "POST",
						data: $("#PostForm").serialize(),
						dataType: "html",
						success: function(data) {
							$("html, body").animate({
								scrollTop: 0
							}, "slow");
							$("#loading").hide();
						},
						error: function(data) {
							$("#loading").hide();
							$.notify("Koneksi gagal", "error");
					
						}
					});
				}
			}
			function PrintAbsen() {
				form = $("#PostForm");
				//form.attr("target", "_blank");
				form.attr("action", "./CustomerService/Absen/Export.php");
				form.submit();
			}
		</script>
	</body>
</html>
