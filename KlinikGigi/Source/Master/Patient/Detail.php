<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		$PatientID = mysql_real_escape_string($_GET['ID']);
		$PatientName = "";
		$PatientNumber = "";
		$BirthDate = "";
		$Address = "";
		$City = "";
		$Telephone = "";
		$Email = "";
		$IsEdit = 0;
		$SalesID = 0;
		$Allergy = "";
		if($PatientID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						PatientID,
						PatientName,
						DATE_FORMAT(BirthDate, '%d-%m-%Y') BirthDate,
						PatientNumber,
						Address,
						City,
						Telephone,
						Email,
						Allergy
					FROM
						master_patient
					WHERE
						PatientID = $PatientID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$PatientID = $row['PatientID'];
			$PatientName = $row['PatientName'];
			$PatientNumber = $row['PatientNumber'];
			$BirthDate = $row['BirthDate'];
			$Address = $row['Address'];
			$City = $row['City'];
			$Telephone = $row['Telephone'];
			$Email = $row['Email'];
			$Allergy = $row['Allergy'];
		}
	}
?>
<html>
	<head>
		<style>
			textarea {
				height: 75px !important;
			}
		</style>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Pasien</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									ID Pasien :
								</div>
								<div class="col-md-3">
									<input id="txtPatientNumber" name="txtPatientNumber" type="text" class="form-control-custom" placeholder="ID Pasien" required   <?php echo 'value="'.$PatientNumber.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Nama Pasien :
								</div>
								<div class="col-md-3">
									<input id="hdnPatientID" name="hdnPatientID" type="hidden" <?php echo 'value="'.$PatientID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="txtPatientName" name="txtPatientName" type="text" class="form-control-custom" placeholder="Nama Pasien" required   <?php echo 'value="'.$PatientName.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Tanggal Lahir :
								</div>
								<div class="col-md-3">
									<input id="txtBirthDate" name="txtBirthDate" type="text" class="form-control-custom DatePickerMonthYearUntilNow" placeholder="Tanggal Lahir" required   <?php echo 'value="'.$BirthDate.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Telepon :
								</div>
								<div class="col-md-3">
									<input id="txtTelephone" name="txtTelephone" maxlength=30 type="text" class="form-control-custom" placeholder="Telepon" <?php echo 'value="'.$Telephone.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Email :
								</div>
								<div class="col-md-3">
									<input id="txtEmail" name="txtEmail" type="text" class="form-control-custom" placeholder="Email" <?php echo 'value="'.$Email.'"'; ?> required />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Alamat:
								</div>
								<div class="col-md-3">
									<textarea id="txtAddress" name="txtAddress" class="form-control-custom" placeholder="Alamat"><?php echo $Address; ?> </textarea>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Kota :
								</div>
								<div class="col-md-3">
									<input id="txtCity" maxlength=30 name="txtCity" type="text" class="form-control-custom" placeholder="Kota" <?php echo 'value="'.$City.'"'; ?> />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Alergi :
								</div>
								<div class="col-md-3">
									<textarea id="txtAllergy" name="txtAllergy" class="form-control-custom" placeholder="Alergi"><?php echo $Allergy; ?> </textarea>
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick="SubmitValidate();" ><i class="fa fa-save"></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				$("#ddlSales").combobox();
			});
			
			function validateEmail(email) {
				var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				return re.test(email);
			}
			
			function SubmitValidate() {
				var PassValidate = 1;
				var FirstFocus = 0;
				var Email = $("#txtEmail").val();
				$(".form-control-custom").each(function() {
					if($(this).hasAttr('required')) {
						if($(this).val() == "") {
							PassValidate = 0;
							$(this).notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
							if(FirstFocus == 0) $(this).focus();
							FirstFocus = 1;
						}
					}
				});
				
				if($("#ddlSales").val() == "") {
					PassValidate = 0;
					$("#ddlSales").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					if(FirstFocus == 0) $("#ddlSales").next().find("input").focus();
					FirstFocus = 1;
				}
				
				if(Email != "") {
					if (!validateEmail(Email)) {
						PassValidate = 0;
						$("#txtEmail").notify("Email tidak valid!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					}
				}
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else {
					SubmitForm('./Master/Patient/Insert.php')
				}
			}
		</script>
	</body>
</html>