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
		$IsEdit = 0;
		if($cek==0) {
			$Content = "Anda Tidak Memiliki Akses Untuk Menu Ini!";
		}
		else {
			if($Id !=0) {
				$IsEdit = 1;
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
							CS.Report
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
				width: 100%;
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
				<h2>Customer Service</h2>   
			</div>
		</div>
		<!-- /. ROW  -->
		<hr />
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<strong><?php if($Id == 0) echo "Tambah"; else echo "Ubah"; ?> Data Customer Service</strong>  
					</div>
					<div class="panel-body">
						<form id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2">Id :</div>
								<div class="col-md-6">
									<input id="hdnId" name="hdnId" type="hidden" <?php echo 'value="'.$Id.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnKlienID" name="hdnKlienID" type="hidden" <?php echo 'value="'.$KlienID.'"'; ?> />
									<input id="hdnJenis" name="hdnJenis" type="hidden" <?php echo 'value="'.$Jenis.'"'; ?> />
									<input id="hdnReport" name="hdnReport" type="hidden" <?php echo 'value="'.$Report.'"'; ?> />
									<input id="txtId" name="txtId" type="text" class="form-control" placeholder="Id" readonly="readonly" <?php echo 'value="'.$Id.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">Tanggal :</div>
								<div class="col-md-6">
									<input id="txtTanggal" name="txtTanggal" type="text" class="form-control DatePickerFromNow" placeholder="Tanggal" required <?php echo 'value="'.$Tanggal.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">Jenis Klien :</div>
								<div class="col-md-6">
									<select class="form-control" name="ddlJenis" id="ddlJenis" required >
										<option value="" selected>-- Pilih Jenis Klien --</option>
										<option value="1">Perusahaan/Industri</option>
										<option value="2">Anak & Remaja</option>
										<option value="4">Dewasa</option>
										<option value="3">Pendidikan</option>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">Nama Klien :</div>
								<div class="col-md-6">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlKlien" id="ddlKlien" class="form-control" >
											<option value="" selected> </option>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">Contact Person :</div>
								<div class="col-md-6">
									<input id="txtCP" name="txtCP" type="text" readonly class="form-control" placeholder="Contact Person" <?php echo 'value="'.$ContactPerson.'"'; ?> />
								</div>
							</div>
							<br />
							<span id="IndustryForm" style="display: none;">
								<div class="row">
									<div class="col-md-2">Maksimal Klien :</div>
									<div class="col-md-6">
										<input id="txtMaksKlien" name="txtMaksKlien" type="text" class="form-control" onkeypress="return isNumberKey(event)" placeholder="Maksimal Klien" <?php echo 'value="'.$MaksKlien.'"'; ?>  IndustryRequired />
									</div>
								</div>
								<br />
							</span>
							<span id="AdultForm" style="display: none;">
								<div class="row">
									<div class="col-md-2">Keluhan :</div>
									<div class="col-md-6">
										<textarea placeholder="Keluhan" id="txtKeluhan" name="txtKeluhan" AdultRequired><?php echo $Keluhan; ?></textarea>
									</div>
								</div>	
								<br />
								<div class="row">
									<div class="col-md-2">Prognosis :</div>
									<div class="col-md-6">
										<textarea placeholder="Prognosis" id="txtPrognosis" name="txtPrognosis" AdultRequired><?php echo $Prognosis; ?></textarea>
									</div>
								</div>
								<br />
							</span>
							<div class="row">
								<div class="col-md-2">Keterangan :</div>
								<div class="col-md-6">
									<textarea placeholder="Keterangan" id="txtKeterangan" name="txtKeterangan" ><?php echo $Keterangan; ?></textarea>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">Report :</div>
								<div class="col-md-6">
									<input type="checkbox" id="cbxReport" name="cbxReport" value=1 />
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitValidate(this.form);" ><i class="fa fa-save"></i> Simpan</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>			
			$(document).ready(function () {
				$("#ddlKlien").combobox();
				var IsEdit = $("#hdnIsEdit").val();
				if(IsEdit == 1) {
					$("#ddlJenis").val($("#hdnJenis").val());
					$.ajax({
						url: "./CustomerService/CustomerService/GetKlien.php",
						type: "POST",
						data: { ddlJenis : $("#hdnJenis").val() },
						dataType: "html",
						success: function(data) {
							$("#ddlKlien").html(data);
							//$("#ddlKlien").find('option:selected').removeAttr("selected");
							$("#ddlKlien option[value='" + $("#hdnKlienID").val() + "']").attr('selected', 'selected');
							$("#ddlKlien").combobox("destroy");
							$("#ddlKlien").combobox();
						},
						error: function(data) {
							$.notify("Koneksi gagal", "error");
							//$("#error").html(data.responseText);
							//$.notify(data.ResponseText, "error");
						}
					});
										
					if($("#hdnReport").val() == 1) {
						$("#cbxReport").attr("checked", true);
						$("#cbxReport").prop("checked", true);
					}
					if($("#hdnJenis").val() == 1 || $("#hdnJenis").val() == 3) {
						$("#IndustryForm").css({
							display: "inline"
						});
						$("#AdultForm").css({
							display: "none"
						});
					}
					else if($("#hdnJenis").val() == 2 || $("#hdnJenis").val() == 4){
						$("#IndustryForm").css({
							display: "none"
						});
						$("#AdultForm").css({
							display: "inline"
						});
					}
					else {
						$("#IndustryForm").css({
							display: "none"
						});
						$("#AdultForm").css({
							display: "none"
						});
					}
				}
				$("#ddlJenis").on("change", function() {
					$.ajax({
						url: "./CustomerService/CustomerService/GetKlien.php",
						type: "POST",
						data: { ddlJenis : $("#ddlJenis").val() },
						dataType: "html",
						success: function(data) {
							$("#ddlKlien").html(data);
						},
						error: function(data) {
							$.notify("Koneksi gagal", "error");
						}
					});
					if($(this).val() == 1 || $(this).val() == 3) {
						$("#IndustryForm").css({
							display: "inline"
						});
						$("#AdultForm").css({
							display: "none"
						});
					}
					else if($(this).val() == 2 || $(this).val() == 4){
						$("#IndustryForm").css({
							display: "none"
						});
						$("#AdultForm").css({
							display: "inline"
						});
					}
					else {
						$("#IndustryForm").css({
							display: "none"
						});
						$("#AdultForm").css({
							display: "none"
						});
					}
				});
			});
			function SubmitValidate(form) {
				var PassValidate = 1;
				var FirstFocus = 0;
				$(".form-control").each(function() {
					if($(this).hasAttr('required')) {
						if($(this).val() == "") {
							PassValidate = 0;
							$(this).notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $(this).focus();
							FirstFocus = 1;
						}
					}
				});
				
				if($("#ddlKlien").val() == "") {
					PassValidate = 0;
					$(".custom-combobox-input").notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $(".custom-combobox-input").focus();
					FirstFocus = 1;
				}
				
				if($("#ddlJenis").val() == 1 || $("#ddlJenis").val() == 3) {
					$(".form-control").each(function() {
						if($(this).hasAttr('IndustryRequired')) {
							if($(this).val() == "") {
								PassValidate = 0;
								$(this).notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
								if(FirstFocus == 0) $(this).focus();
								FirstFocus = 1;
							}
						}
					});
				}
				else if($("#ddlJenis").val() == 2 || $("#ddlJenis").val() == 4) {	
					$(".form-control").each(function() {
						if($(this).hasAttr('AdultRequired')) {
							if($(this).val() == "") {
								PassValidate = 0;
								$(this).notify("Harus diisi!", { position:"right", className:"warn", autoHideDelay: 2000 });
								if(FirstFocus == 0) $(this).focus();
								FirstFocus = 1;
							}
						}
					});
				}
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else SubmitForm("./CustomerService/CustomerService/Insert.php");
			}
		</script>
	</body>
</html>
