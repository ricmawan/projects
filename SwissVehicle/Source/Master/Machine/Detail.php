<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];		
		$MachineID = mysql_real_escape_string($_GET['ID']);
		$MachineKind = "Mobil";
		$MachineType = "";
		$MachineYear = Date('Y');
		$MachineCode = "";
		$BrandName = "";
		$Remarks = "";
		$IsEdit = 0;
		
		if($MachineID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						MachineID,
						MachineKind,
						MachineType,
						MachineYear,
						MachineCode,
						BrandName,
						Remarks
					FROM
						master_machine
					WHERE
						MachineID = $MachineID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$MachineID = $row['MachineID'];
			$MachineKind = $row['MachineKind'];
			$MachineType = $row['MachineType'];
			$MachineYear = $row['MachineYear'];
			$MachineCode = $row['MachineCode'];
			$BrandName = $row['BrandName'];
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data Mobil/Mesin</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Jenis :
								</div>
								<div class="col-md-3">
									<input id="hdnMachineID" name="hdnMachineID" type="hidden" <?php echo 'value="'.$MachineID.'"'; ?> />
									<input id="hdnIsEdit" name="hdnIsEdit" type="hidden" <?php echo 'value="'.$IsEdit.'"'; ?> />
									<input id="hdnMachineKind" name="hdnMachineKind" type="hidden" <?php echo 'value="'.$MachineKind.'"'; ?> />
									<input id="hdnMachineYear" name="hdnMachineYear" type="hidden" <?php echo 'value="'.$MachineYear.'"'; ?> />
									
									<select id="ddlMachineKind" name="ddlMachineKind" class="form-control-custom" >
										<option selected value="Mobil" >Mobil</option>
										<option value="Mesin" >Mesin</option>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Merek :
								</div>
								<div class="col-md-3">
									<input id="txtBrandName" name="txtBrandName" type="text" class="form-control-custom" placeholder="Merek" <?php echo 'value="'.$BrandName.'"'; ?> required />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Tipe :
								</div>
								<div class="col-md-3">
									<input id="txtMachineType" name="txtMachineType" type="text" class="form-control-custom" placeholder="Tipe" <?php echo 'value="'.$MachineType.'"'; ?> required/>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn" id="lblMachineCode" >
									Plat No :
								</div>
								<div class="col-md-3">
									<input id="txtMachineCode" name="txtMachineCode" type="text" class="form-control-custom" placeholder="Plat No" <?php echo 'value="'.$MachineCode.'"'; ?> required />
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Tahun :
								</div>
								<div class="col-md-3">
									<select id="ddlYear" name="ddlYear" class="form-control-custom" >
										<?php
											$MinYear = Date('Y') - 50;
											$MaxYear = Date('Y');
											for($MaxYear;$MaxYear>=$MinYear;$MaxYear--) {
												echo "<option value=$MaxYear >".$MaxYear."</option>";
											}
										?>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									Keterangan :
								</div>
								<div class="col-md-3">
									<textarea id="txtRemarks" name="txtRemarks" class="form-control-custom" placeholder="Keterangan"><?php echo $Remarks; ?></textarea>
								</div>
							</div>
							<br />
							<button type="button" class="btn btn-default" value="Simpan" onclick='SubmitForm("./Master/Machine/Insert.php")' ><i class="fa fa-save"></i> Simpan</button>&nbsp;&nbsp;
							<button type="button" class="btn btn-default" value="Kembali" onclick='Back();' ><i class="fa fa-arrow-circle-left"></i> Kembali</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				$("#ddlMachineKind").val($("#hdnMachineKind").val());
				if($("#ddlMachineKind").val() == "Mobil") {
					$("#lblMachineCode").html("Plat No :");
					$("#txtMachineCode").attr("placeholder", "Plat No");
				}
				else {
					$("#lblMachineCode").html("Kode Mesin :");
					$("#txtMachineCode").attr("placeholder", "Kode Mesin");
				}
				$("#ddlYear").val($("#hdnMachineYear").val());
				$("#ddlMachineKind").change(function() {
					if($("#ddlMachineKind").val() == "Mobil") {
						$("#lblMachineCode").html("Plat No :");
						$("#txtMachineCode").attr("placeholder", "Plat No");
					}
					else {
						$("#lblMachineCode").html("Kode Mesin :");
						$("#txtMachineCode").attr("placeholder", "Kode Mesin");
					}
				});
			});
		</script>
	</body>
</html>
