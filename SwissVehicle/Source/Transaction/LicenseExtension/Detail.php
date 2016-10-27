<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$LicenseExtensionID = mysql_real_escape_string($_GET['ID']);
		$TransactionDate = "";
		$MachineID = "";
		$DueDate = "";
		$Remarks = "";
		$IsEdit = 0;
		$rowCount = 0;
		if($LicenseExtensionID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						LE.LicenseExtensionID,
						DATE_FORMAT(LE.TransactionDate, '%d-%m-%Y') AS TransactionDate,
						LE.MachineID,
						DATE_FORMAT(LE.DueDate, '%d-%m-%Y') AS DueDate,
						LE.Remarks
					FROM
						transaction_licenseextension LE
					WHERE
						LE.LicenseExtensionID = $LicenseExtensionID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$LicenseExtensionID = $row['LicenseExtensionID'];
			$TransactionDate = $row['TransactionDate'];
			$MachineID = $row['MachineID'];
			$DueDate = $row['DueDate'];
			$Remarks = $row['Remarks'];
		}
	}
?>
<html>
	<head>
	</head>
	<body>
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Perpanjang STNK</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Tanggal :
									<input id="hdnLicenseExtensionID" name="hdnLicenseExtensionID" type="hidden" <?php echo 'value="'.$LicenseExtensionID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
								</div>
								<div class="col-md-3">
									<input id="txtTransactionDate" name="txtTransactionDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Tanggal" required <?php echo 'value="'.$TransactionDate.'"'; ?>/>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Mobil :
								</div>
								<div class="col-md-3">
									<div class="ui-widget" style="width: 100%;">
										<select name="ddlMachine" id="ddlMachine" class="form-control-custom" placeholder="Pilih Mobil" >
											<option value="" brandid="" selected> </option>
											<?php
												$sql = "SELECT 
															MM.MachineID,
															MM.MachineCode,
															MM.MachineType
														FROM 
															master_machine MM
														WHERE
															MM.MachineKind = 'Mobil'
														ORDER BY
															MM.MachineCode";
												if(!$result = mysql_query($sql, $dbh)) {
													echo mysql_error();
													return 0;
												}
												while($row = mysql_fetch_array($result)) {
													if($MachineID == $row['MachineID']) echo "<option selected value='".$row['MachineID']."' >".$row['MachineType']." - ".$row['MachineCode']."</option>";
													else echo "<option value='".$row['MachineID']."' >".$row['MachineType']." - ".$row['MachineCode']."</option>";
												}
											?>
										</select>
									</div>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Masa Berlaku :
								</div>
								<div class="col-md-3">
									<input id="txtDueDate" name="txtDueDate" type="text" class="form-control-custom DatePickerMonthYearGlobal" placeholder="Masa Berlaku" required <?php echo 'value="'.$DueDate.'"'; ?>/>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2">
									Keterangan :
								</div>
								<div class="col-md-3">
									<textarea id="txtRemarks" name="txtRemarks" class="form-control-custom" placeholder="Keterangan"><?php echo $Remarks; ?></textarea>
								</div>
							</div>
						</form>
						<br />
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-default" id="btnSave"  onclick="SubmitValidate();" ><i class="fa fa-save "></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function () {
				$("#ddlMachine").combobox();
				$("#ddlMachine").next().find("input").click(function() {
					$(this).val("");
				});
			});
			
			function SubmitValidate() {
				var PassValidate = 1;
				var FirstFocus = 0;
				var MachineID = $("#ddlMachine").val();
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
				
				if(MachineID == 0) {
					$("#ddlMachine").next().find("input").notify("Harus diisi!", { position:"bottom left", className:"warn", autoHideDelay: 2000 });
					PassValidate = 0;
					if(FirstFocus == 0) $("#ddlMachine").next().find("input").focus();
					FirstFocus = 1;
				}
				
				if(PassValidate == 0) {
					$("html, body").animate({
						scrollTop: 0
					}, "slow");
					return false;
				}
				else SubmitForm("./Transaction/LicenseExtension/Insert.php");
			}
		</script>
	</body>
</html>
