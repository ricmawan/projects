<?php
	if(isset($_GET['ID'])) {
		$RequestPath = "$_SERVER[REQUEST_URI]";
		$file = basename($RequestPath);
		$RequestPath = str_replace($file, "", $RequestPath);
		include "../../GetPermission.php";
		//echo $_SERVER['REQUEST_URI'];
		$Content = "";
		$FuelID = mysql_real_escape_string($_GET['ID']);
		$TransactionDate = "";
		$MachineID = "";
		$FuelTypeID = "";
		$Kilometer = "0";
		$Quantity = "0";
		$Price = 0.00;
		$Total = 0.00;
		$Remarks = "";
		$IsEdit = 0;
		$rowCount = 0;
		if($FuelID != 0) {
			$IsEdit = 1;
			//$Content = "Place the content here";
			$sql = "SELECT
						TF.FuelID,
						DATE_FORMAT(TF.TransactionDate, '%d-%m-%Y') AS TransactionDate,
						TF.MachineID,
						TF.Kilometer,
						TF.Quantity,
						TF.Price,
						TF.Remarks,
						TF.FuelTypeID
					FROM
						transaction_fuel TF
					WHERE
						TF.FuelID = $FuelID";
						
			if (! $result=mysql_query($sql, $dbh)) {
				echo mysql_error();
				return 0;
			}				
			$row=mysql_fetch_array($result);
			$FuelID = $row['FuelID'];
			$TransactionDate = $row['TransactionDate'];
			$FuelTypeID = $row['FuelTypeID'];
			$MachineID = $row['MachineID'];
			$Kilometer = $row['Kilometer'];
			$Quantity = $row['Quantity'];
			$Price = $row['Price'];
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
						<h5><?php if($IsEdit == 0) echo "Tambah"; else echo "Ubah"; ?> Data BBM</h5>  
					</div>
					<div class="panel-body">
						<form class="col-md-12" id="PostForm" method="POST" action="" >
							<div class="row">
								<div class="col-md-2 labelColumn">
									Tanggal :
									<input id="hdnFuelID" name="hdnFuelID" type="hidden" <?php echo 'value="'.$FuelID.'"'; ?> />
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
									Jenis BBM :
								</div>
								<div class="col-md-3">
									<select id="ddlFuelType" name="ddlFuelType" class="form-control-custom">
										<option value="" >-Pilih Jenis BBM-</option>
										<?php
											$sql = "SELECT 
														FT.FuelTypeID,
														FT.FuelTypeName,
														FT.Price
													FROM 
														master_fueltype FT
													ORDER BY
														FT.FuelTypeName";
											if(!$result = mysql_query($sql, $dbh)) {
												echo mysql_error();
												return 0;
											}
											while($row = mysql_fetch_array($result)) {
												if($FuelTypeID == $row['FuelTypeID']) echo "<option value='".$row['FuelTypeID']."' price=".$row['Price']." selected>".$row['FuelTypeName']."</option>";
												else echo "<option value='".$row['FuelTypeID']."' price=".$row['Price']." >".$row['FuelTypeName']."</option>";
											}
										?>
									</select>
								</div>
							</div>
							<br />
							<div class="row">
								<div class="col-md-2 labelColumn">
									KM :
								</div>
								<div class="col-md-3">
									<input id="txtKilometer" style="text-align:right;" name="txtKilometer" type="text" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertThousand(this.id, this.value)" class="form-control-custom" placeholder="KM" required <?php echo 'value="'.number_format($Kilometer,0,".",",").'"'; ?>/>
								</div>
							</div>
							<br />
							<div class="row" id="RowItem" >
								<div class="col-md-2 labelColumn">
									Jumlah (Liter) :
								</div>
								<div class="col-md-3">
									<input id="txtQuantity" style="text-align:right;" name="txtQuantity" type="text" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertThousand(this.id, this.value)" class="form-control-custom" placeholder="Jumlah (Liter)" required <?php echo 'value="'.number_format($Quantity,2,".",",").'"'; ?>/>
								</div>
							</div>
							<br />
							<div class="row" id="RowItem" >
								<div class="col-md-2 labelColumn">
									Harga :
								</div>
								<div class="col-md-3">
									<input id="txtPrice" style="text-align:right;" name="txtPrice" type="text" onchange="Calculate();" onkeypress="return isNumberKey(event, this.id, this.value)" onfocus="clearFormat(this.id, this.value)" onblur="convertRupiah(this.id, this.value)" class="form-control-custom" placeholder="Total" required <?php echo 'value="'.number_format($Price,2,".",",").'"'; ?>/>
								</div>
							</div>
							<br />
							<div class="row" id="RowItem" >
								<div class="col-md-2 labelColumn">
									Total :
								</div>
								<div class="col-md-3">
									<input id="txtTotal" style="text-align:right;" name="txtTotal" type="text" class="form-control-custom" placeholder="Total" readonly <?php echo 'value="'.number_format($Total,2,".",",").'"'; ?>/>
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
								<button class="btn btn-default" id="btnAdd" style="display:none;" ><i class="fa fa-plus "></i> Tambah</button>&nbsp;&nbsp;
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
				
				$("#ddlFuelType").change(function() {
					var price = $("#ddlFuelType option:selected").attr("price");
					$("#txtPrice").val(returnRupiah(price));
					Calculate();
				});
			});
			
			function Calculate() {
				var qty = $("#txtQuantity").val().replace(/\,/g, "");
				var price = $("#txtPrice").val().replace(/\,/g, "");
				var total = parseFloat(qty) * parseFloat(price);
				$("#txtTotal").val(returnRupiah(total.toFixed(2).toString()));
			}
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
				else SubmitForm("./Transaction/Fuel/Insert.php");
			}
		</script>
	</body>
</html>
